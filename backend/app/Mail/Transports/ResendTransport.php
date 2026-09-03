<?php

namespace App\Mail\Transports;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class ResendTransport extends AbstractTransport
{
    public function __construct(
        protected string $apiKey,
        protected string $fromAddress = 'onboarding@resend.dev',
        protected string $fromName = 'FinanceFlow'
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $to = array_map(fn ($addr) => $addr->getAddress(), $email->getTo());
        $subject = $email->getSubject();
        $html = $email->getHtmlBody();
        $text = $email->getTextBody();

        $fromHeader = $email->getFrom();
        $from = !empty($fromHeader) ? $fromHeader[0]->toString() : "{$this->fromName} <{$this->fromAddress}>";

        $payload = [
            'from'    => $from,
            'to'      => $to,
            'subject' => $subject,
            'html'    => is_string($html) ? $html : (is_string($text) ? nl2br($text) : ''),
            'text'    => is_string($text) ? $text : '',
        ];

        $response = Http::withToken($this->apiKey)
            ->timeout(10)
            ->post('https://api.resend.com/emails', $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('Resend API Error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'resend';
    }
}
