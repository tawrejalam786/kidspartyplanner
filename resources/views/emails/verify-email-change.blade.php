<h2>Verify your new email</h2>
<p>Hi {{ $user->name }},</p>
<p>We received a request to change your Kids Party Planner account email to <strong>{{ $user->pending_email }}</strong>.</p>
<p><a href="{{ $verificationUrl }}">Verify email address</a></p>
<p>This link is valid for 24 hours. If you did not request this change, you can ignore this email.</p>
