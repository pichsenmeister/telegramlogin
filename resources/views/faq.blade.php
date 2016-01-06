@extends('app')

@section('navbar')
    <li class="active"><a href="/faq">FAQ</a></li>
    <li><a href="/docs">Documentation</a></li>
    @if(Auth::user())
        <li><a href="/dashboard">Dashboard</a></li>
    @endif
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>FAQs</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3>What is Telegram?</h3>
            </div>
            <div class="col-sm-12">
                Telegram is a popular and emerging messenger app with a focus on speed and security.
                Telegram is free and available on all major platforms.
                It can be used on all your devices at the same time, messages are synced seamlessly across any number of your phones, tablets or computers.
                Find out more about Telegram <a href="https://telegram.org" target="_blank">here</a>.
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3>What is TelegramLogin?</h3>
            </div>
            <div class="col-sm-12">
                TelegramLogin is a <a href="https://telegram.org/blog/bot-revolution" target="_blank">Telegram Bot</a>,
                aiming to bring to functionality of OpenID to the Telegram platform.
                TelegramLogin is not affiliated with Telegram or people behind Telegram Messenger in any way whatsoever.

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3>Is TelegramLogin (<a href="https://telegram.me/tglogin_bot" target="_blank">@TgLogin_Bot</a>) an official Telegram bot?</h3>
            </div>
            <div class="col-sm-12">
                No.
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3>Why shoud I use TelegramLogin?</h3>
            </div>
            <div class="col-sm-12">
                TelegramLogin can be used for your web service to login users with their existing Telegram account,
                like other platforms such as Google, Facebook, Twitter and many more already offer.

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3>Which user data will be sent to a webservice using TelegramLogin?</h3>
            </div>
            <div class="col-sm-12">
                TelegramLogin is a normal Telegram Bot, therefore only data which can be retrieved via the Bot API are sent to the webservice.
                With the current state of Telegram's Bot API, the webservice retrieves following data about the user: <strong>Name, Telegram ID, Avatar (soon), Username (if set)</strong>.
                Furthermore, TelegramLogin generates a unique email address for every Telegram user who connects with a webservice via TelegramLogin.
                This is solely a feature of TelegramLogin to forward emails to the TgLogin_Bot chat.
                <strong>NO contacts, chats or messages</strong> will be forwarded to a webservice.
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3>Can users revoke access to a webservice?</h3>
            </div>
            <div class="col-sm-12">
                Yes. Users can revoke access to a webservice by sending the <strong>/revoke</strong> command to the TgLogin_Bot at any time.
                To list currently connected apps the <strong>/list</strong> command can be used.
            </div>
        </div>
    </div>
    <div class="mb"></div>
@endsection
