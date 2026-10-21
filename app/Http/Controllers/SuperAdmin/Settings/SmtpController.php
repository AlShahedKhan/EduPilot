<?php

namespace App\Http\Controllers\SuperAdmin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmtpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index()
    {
        return view('superadmin.settings.smtp_settings');
    }

    public function update(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);

        $map = [
            'smtp_protocol' => 'MAIL_MAILER',
            'smtp_crypto'   => 'MAIL_ENCRYPTION',
            'smtp_host'     => 'MAIL_HOST',
            'smtp_port'     => 'MAIL_PORT',
            'smtp_user'     => 'MAIL_USERNAME',
            'smtp_pass'     => 'MAIL_PASSWORD',
            'smtp_from'     => 'MAIL_FROM_ADDRESS',
        ];

        foreach ($map as $formKey => $configKey) {
            if (isset($data[$formKey]) && $data[$formKey] !== '') {
                set_config($configKey, $data[$formKey]);
            }
        }

        return redirect()->back()->with('message', 'SMTP settings updated successfully.');
    }

    public function test(Request $request)
    {
        $host     = $request->input('smtp_host');
        $port     = (int) $request->input('smtp_port');
        $crypto   = $request->input('smtp_crypto');
        $username = $request->input('smtp_user');
        $password = $request->input('smtp_pass');

        if (empty($host) || empty($port) || empty($username)) {
            return response()->json(['success' => false, 'message' => 'Host, port, and username are required.']);
        }

        try {
            $connection = 'tcp://' . $host . ':' . $port;
            $errno      = 0;
            $errstr     = '';
            $socket     = @stream_socket_client($connection, $errno, $errstr, 10);

            if (!$socket) {
                return response()->json(['success' => false, 'message' => "Could not connect to {$host}:{$port} — {$errstr}"]);
            }

            @fgets($socket, 512);

            if ($port == 587 || $crypto === 'tls') {
                @fwrite($socket, "EHLO school-management\r\n");
                @fgets($socket, 512);

                @fwrite($socket, "STARTTLS\r\n");
                $starttlsResp = @fgets($socket, 512);

                if (strpos($starttlsResp, '220') === 0) {
                    @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
                } else {
                    @fclose($socket);
                    return response()->json(['success' => false, 'message' => 'STARTTLS upgrade failed.']);
                }
            }

            @fwrite($socket, "EHLO school-management\r\n");
            @fgets($socket, 512);

            @fwrite($socket, "AUTH LOGIN\r\n");
            $authResp = @fgets($socket, 512);

            if (strpos($authResp, '334') !== 0) {
                @fclose($socket);
                return response()->json(['success' => false, 'message' => 'SMTP authentication command failed.']);
            }

            @fwrite($socket, base64_encode($username) . "\r\n");
            @fgets($socket, 512);

            @fwrite($socket, base64_encode($password) . "\r\n");
            $passResp = @fgets($socket, 512);

            if (strpos($passResp, '235') !== 0) {
                @fclose($socket);
                return response()->json(['success' => false, 'message' => 'SMTP authentication failed. Check username and password.']);
            }

            @fwrite($socket, "QUIT\r\n");
            @fclose($socket);

            return response()->json(['success' => true, 'message' => 'SMTP connection and authentication successful!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
