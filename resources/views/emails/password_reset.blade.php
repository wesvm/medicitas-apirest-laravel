This is a mail test
@if($user->role === 'admin')
    <span>First Name: {{ $user->admin->first_name }}</span>
@endif

<span>Token: {{ $token }}</span>
