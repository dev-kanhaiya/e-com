<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    /**
     * Send order confirmation email using PHPMailer as requested.
     */
    public static function sendOrderConfirmation(Order $order): bool
    {
        // Check if mail host is configured
        $host = env('MAIL_HOST');
        if (! $host) {
            Log::info("Order confirmation email for Order #{$order->order_number} skipped: MAIL_HOST not configured.");

            return false;
        }

        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', '127.0.0.1');
            $mail->SMTPAuth = ! empty(env('MAIL_USERNAME'));
            $mail->Username = env('MAIL_USERNAME', '');
            $mail->Password = env('MAIL_PASSWORD', '');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port = env('MAIL_PORT', 587);

            // Recipients
            $fromAddress = env('MAIL_FROM_ADDRESS', 'no-reply@e-com.in');
            $fromName = env('MAIL_FROM_NAME', config('app.name', 'E-COM'));
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($order->user->email, $order->user->name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Order Confirmation #{$order->order_number} - E-COM";

            // Build HTML Body
            $itemsHtml = '';
            foreach ($order->items as $item) {
                $itemsHtml .= "<tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item->product_name}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item->quantity}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>₹{$item->price}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>₹{$item->total}</td>
                </tr>";
            }

            $mail->Body = "
                <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; max-width: 620px; margin: 0 auto; padding: 24px; border: 1px solid #e5e7eb; border-radius: 8px; background-color: #ffffff; color: #111827;'>
                    <div style='border-bottom: 2px solid #2563eb; padding-bottom: 16px; margin-bottom: 20px;'>
                        <h1 style='color: #111827; font-size: 22px; margin: 0; font-weight: 700;'>E-<span style='color: #2563eb;'>COM</span></h1>
                        <p style='color: #6b7280; font-size: 13px; margin: 4px 0 0;'>Official Order Confirmation & Invoice Receipt</p>
                    </div>

                    <h2 style='color: #166534; font-size: 18px; margin-top: 0;'>Thank you for your order, {$order->user->name}!</h2>
                    <p style='font-size: 14px; line-height: 1.5; color: #374151;'>We have received your order <strong>#{$order->order_number}</strong> and our sellers are currently processing it for shipment.</p>

                    <div style='background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; margin: 18px 0; font-size: 13px; line-height: 1.6;'>
                        <div><strong>Order Reference:</strong> {$order->order_number}</div>
                        <div><strong>Order Date:</strong> " . now()->format('d M Y, h:i A') . "</div>
                        <div><strong>Fulfillment Status:</strong> " . ucfirst($order->status) . "</div>
                        <div><strong>Payment Status:</strong> " . ucfirst($order->payment_status) . " (" . strtoupper($order->payment ? $order->payment->method : 'Prepaid') . ")</div>
                    </div>

                    <div style='margin: 18px 0;'>
                        <h3 style='font-size: 14px; text-transform: uppercase; color: #6b7280; margin-bottom: 8px; letter-spacing: 0.05em;'>Shipping Destination</h3>
                        <p style='font-size: 13px; color: #111827; margin: 0; white-space: pre-line; line-height: 1.5;'>{$order->shipping_address}</p>
                    </div>

                    <h3 style='font-size: 14px; text-transform: uppercase; color: #6b7280; margin-bottom: 8px; letter-spacing: 0.05em;'>Order Items</h3>
                    <table style='width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;'>
                        <thead>
                            <tr style='background-color: #f1f5f9; border-bottom: 1px solid #cbd5e1;'>
                                <th style='padding: 10px 12px; color: #475569;'>Item</th>
                                <th style='padding: 10px 12px; color: #475569;'>Qty</th>
                                <th style='padding: 10px 12px; color: #475569;'>Unit Price</th>
                                <th style='padding: 10px 12px; color: #475569; text-align: right;'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                    </table>

                    <div style='margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 12px; font-size: 13px; line-height: 1.7; text-align: right;'>
                        <div>Subtotal: <strong>₹" . number_format($order->subtotal, 0) . "</strong></div>
                        <div>Shipping & Handling: <strong>₹" . number_format($order->shipping_fee, 0) . "</strong></div>
                        <div style='font-size: 17px; color: #2563eb; margin-top: 4px;'>Grand Total: <strong>₹" . number_format($order->total, 0) . "</strong></div>
                    </div>

                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 24px 0 16px;'>
                    <div style='font-size: 12px; color: #6b7280; line-height: 1.5;'>
                        <strong>E-COM India Pvt. Ltd.</strong><br>
                        Plot 14, Commercial Complex, Ahinsa Khand II, Indirapuram, Ghaziabad, Uttar Pradesh 201014<br>
                        Support: support@e-com.in | +91 120 4567890
                    </div>
                </div>
            ";

            $mail->send();
            Log::info("Order confirmation email sent to {$order->user->email} for Order #{$order->order_number}");

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send order confirmation email: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Send new password to registered user email.
     */
    public static function sendPasswordResetMail(User $user, string $newPassword): bool
    {
        $host = env('MAIL_HOST');
        if (! $host) {
            Log::info("Password reset email for {$user->email} skipped: MAIL_HOST not configured.");

            return false;
        }

        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', '127.0.0.1');
            $mail->SMTPAuth = ! empty(env('MAIL_USERNAME'));
            $mail->Username = env('MAIL_USERNAME', '');
            $mail->Password = env('MAIL_PASSWORD', '');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port = env('MAIL_PORT', 587);

            // Recipients
            $fromAddress = env('MAIL_FROM_ADDRESS', 'no-reply@e-com.in');
            $fromName = env('MAIL_FROM_NAME', config('app.name', 'E-COM'));
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($user->email, $user->name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your New Password - E-COM";

            $loginUrl = route('login');

            $mail->Body = "
                <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #e5e7eb; border-radius: 8px; background-color: #ffffff; color: #111827;'>
                    <div style='border-bottom: 2px solid #2563eb; padding-bottom: 16px; margin-bottom: 20px;'>
                        <h1 style='color: #111827; font-size: 22px; margin: 0; font-weight: 700;'>E-<span style='color: #2563eb;'>COM</span></h1>
                        <p style='color: #6b7280; font-size: 13px; margin: 4px 0 0;'>Password Reset Request</p>
                    </div>

                    <h2 style='font-size: 18px; margin-top: 0; color: #111827;'>Hello, {$user->name}!</h2>
                    <p style='font-size: 14px; line-height: 1.5; color: #374151;'>We received a request to reset your password. A new temporary password has been generated for your account:</p>

                    <div style='background-color: #f8fafc; border: 1px dashed #2563eb; border-radius: 8px; padding: 18px; margin: 24px 0; text-align: center;'>
                        <div style='font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 6px;'>Your New Password</div>
                        <div style='font-size: 24px; font-weight: 700; letter-spacing: 2px; font-family: monospace; color: #2563eb;'>{$newPassword}</div>
                    </div>

                    <p style='font-size: 14px; line-height: 1.5; color: #374151;'>You can now log in using this password. For your security, we recommend that you change this password from your profile settings after logging in.</p>

                    <div style='margin: 24px 0; text-align: center;'>
                        <a href='{$loginUrl}' style='display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; font-size: 14px;'>Login to E-COM</a>
                    </div>

                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 24px 0 16px;'>
                    <div style='font-size: 12px; color: #6b7280; line-height: 1.5;'>
                        <strong>E-COM India Pvt. Ltd.</strong><br>
                        Plot 14, Commercial Complex, Ahinsa Khand II, Indirapuram, Ghaziabad, Uttar Pradesh 201014<br>
                        Support: support@e-com.in | +91 120 4567890
                    </div>
                </div>
            ";

            $mail->send();
            Log::info("Password reset email sent to {$user->email}");

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send password reset email to {$user->email}: {$e->getMessage()}");

            return false;
        }
    }
}
