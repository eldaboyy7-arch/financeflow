<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Hardened Identity Resolver Function (Safe search_path, STABLE, SECURITY DEFINER)
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.requesting_user_id() 
            RETURNS bigint AS $$
            DECLARE
                raw_sub text;
                raw_claims text;
                raw_app text;
            BEGIN
                -- A. Try request.jwt.claim.sub
                raw_sub := pg_catalog.current_setting('request.jwt.claim.sub', true);
                IF raw_sub IS NOT NULL AND raw_sub ~ '^[0-9]+$' THEN
                    RETURN raw_sub::bigint;
                END IF;

                -- B. Try app.current_user_id (Injected context)
                raw_app := pg_catalog.current_setting('app.current_user_id', true);
                IF raw_app IS NOT NULL AND raw_app ~ '^[0-9]+$' THEN
                    RETURN raw_app::bigint;
                END IF;

                -- C. Try request.jwt.claims JSON object
                raw_claims := pg_catalog.current_setting('request.jwt.claims', true);
                IF raw_claims IS NOT NULL AND raw_claims <> '' THEN
                    BEGIN
                        raw_sub := pg_catalog.jsonb_extract_path_text(raw_claims::jsonb, 'user_id');
                        IF raw_sub IS NOT NULL AND raw_sub ~ '^[0-9]+$' THEN
                            RETURN raw_sub::bigint;
                        END IF;
                    EXCEPTION WHEN OTHERS THEN
                        NULL;
                    END;
                END IF;

                -- D. Unknown, UUID, missing, or malformed -> Return NULL (Fail-Closed)
                RETURN NULL;
            EXCEPTION WHEN OTHERS THEN
                RETURN NULL;
            END;
            $$ 
            LANGUAGE plpgsql 
            STABLE 
            SECURITY DEFINER
            SET search_path = pg_catalog, public;

            -- Function Execution Permissions Hardening
            REVOKE ALL ON FUNCTION public.requesting_user_id() FROM PUBLIC;
            REVOKE ALL ON FUNCTION public.requesting_user_id() FROM anon;
            GRANT EXECUTE ON FUNCTION public.requesting_user_id() TO authenticated;
        ");

        // 2. Grant and Revoke Table Privileges
        $tables = [
            'accounts', 'categories', 'transactions', 'transfers', 'budgets',
            'goals', 'goal_contributions', 'recurring_transactions', 'notifications'
        ];

        DB::unprepared("
            GRANT USAGE ON SCHEMA public TO authenticated;
            GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO authenticated;
        ");

        foreach ($tables as $table) {
            DB::unprepared("
                GRANT SELECT, INSERT, UPDATE, DELETE ON public.{$table} TO authenticated;
                REVOKE ALL ON public.{$table} FROM anon;
                ALTER TABLE public.{$table} ENABLE ROW LEVEL SECURITY;
            ");
        }

        // Clean up any legacy or duplicate policies
        DB::unprepared("
            DROP POLICY IF EXISTS user_isolation_policy ON public.transactions;
            DROP POLICY IF EXISTS test_policy ON public.transactions;
            DROP POLICY IF EXISTS accounts_isolation_policy ON public.accounts;
            DROP POLICY IF EXISTS goals_isolation_policy ON public.goals;
            DROP POLICY IF EXISTS notifications_isolation_policy ON public.notifications;
            DROP POLICY IF EXISTS categories_select_policy ON public.categories;
            DROP POLICY IF EXISTS categories_modify_policy ON public.categories;
            DROP POLICY IF EXISTS transactions_isolation_policy ON public.transactions;
            DROP POLICY IF EXISTS transfers_isolation_policy ON public.transfers;
            DROP POLICY IF EXISTS budgets_isolation_policy ON public.budgets;
            DROP POLICY IF EXISTS goal_contributions_isolation_policy ON public.goal_contributions;
            DROP POLICY IF EXISTS recurring_isolation_policy ON public.recurring_transactions;
        ");

        // 3. Create Airtight Relational RLS Policies
        DB::unprepared("
            -- ACCOUNTS
            CREATE POLICY accounts_isolation_policy ON public.accounts
            FOR ALL TO authenticated
            USING (user_id = public.requesting_user_id())
            WITH CHECK (user_id = public.requesting_user_id());

            -- CATEGORIES (SELECT: own or system default; MODIFY: own only)
            CREATE POLICY categories_select_policy ON public.categories
            FOR SELECT TO authenticated
            USING (user_id = public.requesting_user_id() OR user_id IS NULL);

            CREATE POLICY categories_modify_policy ON public.categories
            FOR ALL TO authenticated
            USING (user_id = public.requesting_user_id())
            WITH CHECK (user_id = public.requesting_user_id());

            -- GOALS (Relational: user_id AND own optional account_id)
            CREATE POLICY goals_isolation_policy ON public.goals
            FOR ALL TO authenticated
            USING (
                user_id = public.requesting_user_id()
                AND (
                    account_id IS NULL 
                    OR EXISTS (
                        SELECT 1 FROM public.accounts a 
                        WHERE a.id = goals.account_id 
                          AND a.user_id = public.requesting_user_id()
                    )
                )
            )
            WITH CHECK (
                user_id = public.requesting_user_id()
                AND (
                    account_id IS NULL 
                    OR EXISTS (
                        SELECT 1 FROM public.accounts a 
                        WHERE a.id = goals.account_id 
                          AND a.user_id = public.requesting_user_id()
                    )
                )
            );

            -- GOAL CONTRIBUTIONS (Relational: user_id AND own parent goal AND own optional account_id)
            CREATE POLICY goal_contributions_isolation_policy ON public.goal_contributions
            FOR ALL TO authenticated
            USING (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.goals g 
                    WHERE g.id = goal_contributions.goal_id 
                      AND g.user_id = public.requesting_user_id()
                )
                AND (
                    account_id IS NULL 
                    OR EXISTS (
                        SELECT 1 FROM public.accounts a 
                        WHERE a.id = goal_contributions.account_id 
                          AND a.user_id = public.requesting_user_id()
                    )
                )
            )
            WITH CHECK (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.goals g 
                    WHERE g.id = goal_contributions.goal_id 
                      AND g.user_id = public.requesting_user_id()
                )
                AND (
                    account_id IS NULL 
                    OR EXISTS (
                        SELECT 1 FROM public.accounts a 
                        WHERE a.id = goal_contributions.account_id 
                          AND a.user_id = public.requesting_user_id()
                    )
                )
            );

            -- TRANSACTIONS (Relational: user_id AND own account AND own/default category)
            CREATE POLICY transactions_isolation_policy ON public.transactions
            FOR ALL TO authenticated
            USING (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.accounts a 
                    WHERE a.id = transactions.account_id 
                      AND a.user_id = public.requesting_user_id()
                )
                AND EXISTS (
                    SELECT 1 FROM public.categories c 
                    WHERE c.id = transactions.category_id 
                      AND (c.user_id = public.requesting_user_id() OR c.user_id IS NULL)
                )
            )
            WITH CHECK (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.accounts a 
                    WHERE a.id = transactions.account_id 
                      AND a.user_id = public.requesting_user_id()
                )
                AND EXISTS (
                    SELECT 1 FROM public.categories c 
                    WHERE c.id = transactions.category_id 
                      AND (c.user_id = public.requesting_user_id() OR c.user_id IS NULL)
                )
            );

            -- TRANSFERS (Relational: user_id AND own from_account AND own to_account)
            CREATE POLICY transfers_isolation_policy ON public.transfers
            FOR ALL TO authenticated
            USING (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.accounts a1 
                    WHERE a1.id = transfers.from_account_id 
                      AND a1.user_id = public.requesting_user_id()
                )
                AND EXISTS (
                    SELECT 1 FROM public.accounts a2 
                    WHERE a2.id = transfers.to_account_id 
                      AND a2.user_id = public.requesting_user_id()
                )
            )
            WITH CHECK (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.accounts a1 
                    WHERE a1.id = transfers.from_account_id 
                      AND a1.user_id = public.requesting_user_id()
                )
                AND EXISTS (
                    SELECT 1 FROM public.accounts a2 
                    WHERE a2.id = transfers.to_account_id 
                      AND a2.user_id = public.requesting_user_id()
                )
            );

            -- BUDGETS (Relational: user_id AND own/default category)
            CREATE POLICY budgets_isolation_policy ON public.budgets
            FOR ALL TO authenticated
            USING (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.categories c 
                    WHERE c.id = budgets.category_id 
                      AND (c.user_id = public.requesting_user_id() OR c.user_id IS NULL)
                )
            )
            WITH CHECK (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.categories c 
                    WHERE c.id = budgets.category_id 
                      AND (c.user_id = public.requesting_user_id() OR c.user_id IS NULL)
                )
            );

            -- RECURRING TRANSACTIONS (Relational: user_id AND own account AND own/default category)
            CREATE POLICY recurring_isolation_policy ON public.recurring_transactions
            FOR ALL TO authenticated
            USING (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.accounts a 
                    WHERE a.id = recurring_transactions.account_id 
                      AND a.user_id = public.requesting_user_id()
                )
                AND EXISTS (
                    SELECT 1 FROM public.categories c 
                    WHERE c.id = recurring_transactions.category_id 
                      AND (c.user_id = public.requesting_user_id() OR c.user_id IS NULL)
                )
            )
            WITH CHECK (
                user_id = public.requesting_user_id()
                AND EXISTS (
                    SELECT 1 FROM public.accounts a 
                    WHERE a.id = recurring_transactions.account_id 
                      AND a.user_id = public.requesting_user_id()
                )
                AND EXISTS (
                    SELECT 1 FROM public.categories c 
                    WHERE c.id = recurring_transactions.category_id 
                      AND (c.user_id = public.requesting_user_id() OR c.user_id IS NULL)
                )
            );

            -- NOTIFICATIONS
            CREATE POLICY notifications_isolation_policy ON public.notifications
            FOR ALL TO authenticated
            USING (user_id = public.requesting_user_id())
            WITH CHECK (user_id = public.requesting_user_id());
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $policies = [
            'accounts'               => ['accounts_isolation_policy'],
            'goals'                  => ['goals_isolation_policy'],
            'notifications'          => ['notifications_isolation_policy'],
            'categories'             => ['categories_select_policy', 'categories_modify_policy'],
            'transactions'           => ['transactions_isolation_policy'],
            'transfers'              => ['transfers_isolation_policy'],
            'budgets'                => ['budgets_isolation_policy'],
            'goal_contributions'     => ['goal_contributions_isolation_policy'],
            'recurring_transactions' => ['recurring_isolation_policy'],
        ];

        foreach ($policies as $table => $tablePolicies) {
            foreach ($tablePolicies as $policy) {
                DB::unprepared("DROP POLICY IF EXISTS \"{$policy}\" ON public.{$table};");
            }
            DB::unprepared("ALTER TABLE public.{$table} DISABLE ROW LEVEL SECURITY;");
        }

        DB::unprepared("DROP FUNCTION IF EXISTS public.requesting_user_id();");
    }
};
