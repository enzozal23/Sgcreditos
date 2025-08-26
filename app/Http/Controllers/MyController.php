<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MyController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        //
    }

    /**
     * Método para obtener la IP del cliente
     */
    public function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Método para enviar emails
     */
    public function enviar_email($to, $body, $subject)
    {
        try {
            // Crear una clase simple para el email
            $emailData = [
                'to' => $to,
                'body' => $body,
                'subject' => $subject
            ];

            // Enviar el email usando Mail facade
            Mail::send([], [], function ($message) use ($emailData) {
                $message->to($emailData['to'])
                        ->subject($emailData['subject'])
                        ->html($emailData['body']);
            });

            return true;
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error enviando email: ' . $e->getMessage());
            return false;
        }
    }
}
