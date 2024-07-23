@extends('emails.layout')


@section('content')
    <p>Dear User</p>
    <p>Please use this code to complete your verification process.</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td><p class="lead">{{ $verification_code }}</p></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <p>If you have received this email unintentionally, please delete it.</p>
    <p>Have a great day!</p>
@endsection
