@extends('superadmin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>{{ get_phrase('SMTP Settings') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="eSection-wrap">
            <div class="eMain">
                <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.smtp.update') }}">
                    @csrf
                    <div class="fpb-7">
                        <label for="smtp_protocol" class="eForm-label">{{ get_phrase('Mail Driver') }} <small>(smtp / sendmail / mailgun / ses)</small></label>
                        <input type="text" class="form-control eForm-control" value="{{ get_mail_config('MAIL_MAILER', 'smtp') }}" id="smtp_protocol" name="smtp_protocol" required>
                    </div>
                    <div class="fpb-7">
                        <label for="smtp_host" class="eForm-label">{{ get_phrase('SMTP Host') }}</label>
                        <input type="text" class="form-control eForm-control" value="{{ get_mail_config('MAIL_HOST') }}" id="smtp_host" name="smtp_host" required placeholder="smtp.gmail.com">
                    </div>
                    <div class="fpb-7">
                        <label for="smtp_port" class="eForm-label">{{ get_phrase('SMTP Port') }}</label>
                        <input type="text" class="form-control eForm-control" value="{{ get_mail_config('MAIL_PORT') }}" id="smtp_port" name="smtp_port" required placeholder="587">
                    </div>
                    <div class="fpb-7">
                        <label for="smtp_crypto" class="eForm-label">{{ get_phrase('Encryption') }} <small>(ssl / tls / null)</small></label>
                        <input type="text" class="form-control eForm-control" value="{{ get_mail_config('MAIL_ENCRYPTION') }}" id="smtp_crypto" name="smtp_crypto" required placeholder="tls">
                    </div>
                    <div class="fpb-7">
                        <label for="smtp_user" class="eForm-label">{{ get_phrase('SMTP Username') }}</label>
                        <input type="text" class="form-control eForm-control" value="{{ get_mail_config('MAIL_USERNAME') }}" id="smtp_user" name="smtp_user" required>
                    </div>
                    <div class="fpb-7">
                        <label for="smtp_pass" class="eForm-label">{{ get_phrase('SMTP Password') }}</label>
                        <input type="password" class="form-control eForm-control" value="{{ get_mail_config('MAIL_PASSWORD') }}" id="smtp_pass" name="smtp_pass" required>
                    </div>
                    <div class="fpb-7">
                        <label for="smtp_from" class="eForm-label">{{ get_phrase('From Email') }}</label>
                        <input type="email" class="form-control eForm-control" value="{{ get_mail_config('MAIL_FROM_ADDRESS') }}" id="smtp_from" name="smtp_from" required>
                    </div>
                    <div class="fpb-7 pt-2 d-flex gap-2">
                        <button type="submit" class="btn-form">{{ get_phrase('Save Settings') }}</button>
                        <button type="button" class="btn-form" style="background:#22c55e;" onclick="testSmtpConnection()">{{ get_phrase('Test Connection') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="eSection-wrap">
            <div class="eMain">
                <h5 style="font-size:15px;font-weight:700;margin-bottom:12px;">{{ get_phrase('Current Configuration') }}</h5>
                <table class="table" style="font-size:13px;">
                    <tr><td style="width:40%;color:#64748b;">{{ get_phrase('Driver') }}</td><td><strong>{{ get_mail_config('MAIL_MAILER', 'smtp') }}</strong></td></tr>
                    <tr><td style="color:#64748b;">{{ get_phrase('Host') }}</td><td><strong>{{ get_mail_config('MAIL_HOST') }}</strong></td></tr>
                    <tr><td style="color:#64748b;">{{ get_phrase('Port') }}</td><td><strong>{{ get_mail_config('MAIL_PORT') }}</strong></td></tr>
                    <tr><td style="color:#64748b;">{{ get_phrase('Encryption') }}</td><td><strong>{{ get_mail_config('MAIL_ENCRYPTION') }}</strong></td></tr>
                    <tr><td style="color:#64748b;">{{ get_phrase('Username') }}</td><td><strong>{{ get_mail_config('MAIL_USERNAME') }}</strong></td></tr>
                    <tr><td style="color:#64748b;">{{ get_phrase('From') }}</td><td><strong>{{ get_mail_config('MAIL_FROM_ADDRESS') }}</strong></td></tr>
                </table>
            </div>
        </div>

        <div class="eSection-wrap mt-3">
            <div class="eMain">
                <h5 style="font-size:15px;font-weight:700;margin-bottom:8px;">{{ get_phrase('Test Result') }}</h5>
                <div id="smtpTestResult" style="font-size:13px;color:#64748b;">{{ get_phrase('Click "Test Connection" to verify your SMTP settings.') }}</div>
            </div>
        </div>
    </div>
</div>

<script>
function testSmtpConnection() {
    var resultDiv = document.getElementById('smtpTestResult');
    resultDiv.innerHTML = '<span style="color:#f59e0b;">⏳ {{ get_phrase("Testing connection...") }}</span>';

    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('smtp_protocol', document.getElementById('smtp_protocol').value);
    formData.append('smtp_host', document.getElementById('smtp_host').value);
    formData.append('smtp_port', document.getElementById('smtp_port').value);
    formData.append('smtp_crypto', document.getElementById('smtp_crypto').value);
    formData.append('smtp_user', document.getElementById('smtp_user').value);
    formData.append('smtp_pass', document.getElementById('smtp_pass').value);
    formData.append('smtp_from', document.getElementById('smtp_from').value);

    fetch('{{ route("superadmin.smtp.test") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            resultDiv.innerHTML = '<span style="color:#22c55e;">✅ ' + data.message + '</span>';
        } else {
            resultDiv.innerHTML = '<span style="color:#ef4444;">❌ ' + data.message + '</span>';
        }
    })
    .catch(function(err) {
        resultDiv.innerHTML = '<span style="color:#ef4444;">❌ {{ get_phrase("Connection failed") }}: ' + err.message + '</span>';
    });
}
</script>
@endsection
