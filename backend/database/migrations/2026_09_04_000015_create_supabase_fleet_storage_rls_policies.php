<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Hardens Supabase Storage 'fleet' bucket with tenant-isolated RLS policies.
     */
    public function up(): void
    {
        // Only execute on PostgreSQL (Supabase production environment)
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Verify storage schema exists (Supabase specific)
        $storageExists = DB::select("SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'storage'");
        if (empty($storageExists)) {
            return;
        }

        DB::unprepared("
            -- 1. Ensure 'fleet' public bucket exists
            INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
            VALUES (
                'fleet',
                'fleet',
                true,
                3145728, -- 3MB limit
                ARRAY['image/jpeg', 'image/png', 'image/webp', 'image/avif']
            )
            ON CONFLICT (id) DO UPDATE SET
                public = true,
                file_size_limit = 3145728,
                allowed_mime_types = ARRAY['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

            -- 2. Drop existing policies if any
            DROP POLICY IF EXISTS \"Fleet Public Read Policy\" ON storage.objects;
            DROP POLICY IF EXISTS \"Fleet Tenant Insert Policy\" ON storage.objects;
            DROP POLICY IF EXISTS \"Fleet Tenant Update Policy\" ON storage.objects;
            DROP POLICY IF EXISTS \"Fleet Tenant Delete Policy\" ON storage.objects;

            -- 3. Public Read Policy: anyone can view fleet photos
            CREATE POLICY \"Fleet Public Read Policy\" ON storage.objects
            FOR SELECT TO public
            USING (bucket_id = 'fleet');

            -- 4. Tenant-Isolated Insert: User can only upload into folder matching their auth.uid()
            CREATE POLICY \"Fleet Tenant Insert Policy\" ON storage.objects
            FOR INSERT TO authenticated
            WITH CHECK (
                bucket_id = 'fleet'
                AND (storage.foldername(name))[1] = auth.uid()::text
            );

            -- 5. Tenant-Isolated Update: User can only modify files in their own folder
            CREATE POLICY \"Fleet Tenant Update Policy\" ON storage.objects
            FOR UPDATE TO authenticated
            USING (
                bucket_id = 'fleet'
                AND (storage.foldername(name))[1] = auth.uid()::text
            )
            WITH CHECK (
                bucket_id = 'fleet'
                AND (storage.foldername(name))[1] = auth.uid()::text
            );

            -- 6. Tenant-Isolated Delete: User can only delete files in their own folder
            CREATE POLICY \"Fleet Tenant Delete Policy\" ON storage.objects
            FOR DELETE TO authenticated
            USING (
                bucket_id = 'fleet'
                AND (storage.foldername(name))[1] = auth.uid()::text
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $storageExists = DB::select("SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'storage'");
        if (empty($storageExists)) {
            return;
        }

        DB::unprepared("
            DROP POLICY IF EXISTS \"Fleet Public Read Policy\" ON storage.objects;
            DROP POLICY IF EXISTS \"Fleet Tenant Insert Policy\" ON storage.objects;
            DROP POLICY IF EXISTS \"Fleet Tenant Update Policy\" ON storage.objects;
            DROP POLICY IF EXISTS \"Fleet Tenant Delete Policy\" ON storage.objects;
        ");
    }
};
