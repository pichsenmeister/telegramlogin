@extends('app')

@section('navbar')
    <li class="active"><a href="/docs">Documentation</a></li>
    @if(Auth::user())
        <li><a href="/dashboard">Dashboard</a></li>
    @endif
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Documentation</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <ul>
                    <li>
                        <a href="#setting-up-telegramlogin">Setting up TelegramLogin</a>
                        <ol>
                            <li><a href="#create-app">Create app</a></li>
                            <li><a href="#set-redirect-url">Set redirect URL</a></li>
                            <li><a href="#obtain-credentials">Obtain credentials</a></li>
                        </ol>
                    </li>
                    <li>
                        <a href="#authenticate-users">Authenticate users</a>
                        <ol>
                            <li><a href="#create-anti-forgery-state-token">Create anti forgery state token</a></li>
                            <li><a href="#send-authentication-request-to-telegramlogin">Send authentication request to TelegramLogin</a></li>
                            <li><a href="#confirm-anti-forgery-state-token">Confirm anti forgery state token</a></li>
                            <li><a href="#exchange-code-for-access-token">Exchange code for access token</a></li>
                            <li><a href="#obtain-user-information-from-access-token">Obtain user information from access token</a></li>
                        </ol>
                    </li>
                    <li>
                        <a href="#create-telegramlogin-button">Create TelegramLogin button example</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 id="setting-up-telegramlogin">Setting up TelegramLogin</h2>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    Before your service can use TelegramLogin's authentication system for user login,
                    you have to register an app in the TelegramLogin Dashboard to retrieve your credentials,
                    set a redirect URL and set a name and website (optional) that your users see on Telegram.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="create-app">1. Create app</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    You need TelegramLogin credentials, including a client ID and client secret,
                    to authenticate users via TelegramLogin's API. Therefore you have to register an app.

                    To get your apps's Client ID and Cient Secret, register an app via the <a href="/dashboard">Dashboard</a>:
                </p>
                <p>
                    <i>Screenshot:</i><br>
                    <img src="/img/docs/create_app.png" class="docs-img" alt="Create app screen">
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="set-redirect-url">2. Set redirect URL</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    The redirect URL that you set, will be sent to the users Telegram app including the code and the state key,
                    which you can later exchange for an access token (see <a href="#exchange-code-for-access-token">Exchange code for Access Token</a>).
                    <br>
                    <br>
                    E.g. if your redirect URL is <code>https://telegramlogin.com/login</code>,
                    users will receive following link in Telegram with <code>code</code> and <code>state</code> as <code>GET</code> parameters:
                </p>
                <p>
                    <i>Screenshot:</i><br>
                    <img src="/img/docs/tg_code.jpg" class="docs-img" alt="redirect url screen in Telegram">
                </p>
                <p>

                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="obtain-credentials">3. Obtain credentials</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    Your TelegramLogin credentials, including client ID and client secret,
                    will be generated automatically and will be shown in the <i>My apps</i> overview.
                    <br>
                    Your client ID is your public identifier, but <strong>never</strong> share your client secret!
                </p>
                <p>
                    <i>Screenshot:</i><br>
                    <img src="/img/docs/app_ready.png" class="docs-img" alt="Create app screen">
                </p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 id="authenticate-users">Authenticate Users</h2>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    Authenticating users involves obtaining an access token.
                    Access tokens are a standardized feature and are designed for use in sharing users' identity.
                    TelegramLogin's API flow allows the back-end server of a websevice to verify a user's identity using Telegram Messenger, either on Desktop or mobile devices.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="create-anti-forgery-state-token">1. Create anti forgery state token</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger">This step is highly recommended, but can be skipped if you don't care about security.</div>
                <p>
                    You must protect the security of your users by preventing request forgery attacks.
                    The first step is creating a unique session token that holds state between your app and the user's client.
                    You later match this unique session token with the redirect URL response returned by Telegram to verify that the user is
                    making the request and not a malicious attacker.
                    These tokens are often referred to as cross-site request forgery (CSRF) tokens
                    and will be referred as &lt;your_csrf_token&gt; in this document.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="send-authentication-request-to-telegramlogin">2. Send authentication request to TelegramLogin</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    The next step is to redirect the user to following url,
                    containing your previously created unique state token:
                    <br>
                    <code>https://telegramlogin.com/token/&lt;your_client_id&gt;?state=&lt;your_csrf_token&gt;</code>
                    <br>
                    where <code>&lt;your_client_id&gt;</code> is your app's client ID and
                    <code>&lt;your_csrf_token&gt;</code> is your state token.
                </p>

                <div class="alert alert-info">
                    <strong>Background information:</strong>
                    TelegramLogin creates a unique token on this endpoint to recognize the Telegram user to your app
                    and redirects the user to <code>https://telegram.me/TgLogin_Bot?start=&lt;unique_token&gt;</code>.
                    The <code>https://telegram.me</code> endpoint automatically tries to open
                    the installed Telegram app. When the user clicks the <strong>Start</strong> button
                    of the Bot (within the Telegram app), an access token for this Telegram user is automatically saved at TelegramLogin.
                    The TgLogin_Bot replies with your specified redirect URL with a <code>code</code> parameter (and your <code>state</code> token),
                    which can be <a href="#exchange-code-for-access-token">later exchanged for an access token</a>.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="confirm-anti-forgery-state-token">3. Confirm anti forgery state token</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    The user will receive your redirect URL via Telegram, containing the <code>code</code>
                    and <code>state</code> as <code>GET</code> parameters.
                    On the server, you must confirm that the state received from Telegram matches the session token
                    you created in <a href="#create-anti-forgery-state-token">Step 1</a>.
                    This round-trip verification helps to ensure that the user, not a malicious script, is making the request.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="exchange-code-for-access-token">4. Exchange code for access token</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    The response includes a code parameter, a <strong>one-time</strong> authorization code
                    that your server can exchange for an access token.
                    Your server can make this exchange by sending an <code>HTTPS POST</code> request.
                </p>
                <p>
                    The endpoint for the code exchange is <code>https://telegramlogin.com/code</code> and
                    must contain these parameters:
                </p>
                <p>
                    <code>
                        <strong>code</strong>: the retrieved code parameter
                        <br>
                        <strong>client_id</strong>: your client ID
                        <br>
                        <strong>client_secret</strong>: your client secret
                        <br>
                    </code>
                </p>
                <p>
                    <i>Example request:</i>
                    <br>
                    <pre>POST /code
Host: telegramlogin.com
Content-Type: application/x-www-form-urlencoded

code=&lt;retrieved_code&gt;&
client_id=&lt;your_client_id&gt;&
client_secret=&lt;your_client_secret&gt;</pre>
                </p>
                <p>
                    A successful response to this request contains the TelegramLogin auth object as JSON:
<pre id="telegramlogin-user-response">{
    id: 42,
    email: "xxxxxxxxxx@telegramlogin.com",
    access_token: "ADD9PGxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    active: 1,
    created_at: "2015-03-14 09:26:59",
    telegram_user: {
        telegram_id: 31415265,
        name: "David Pichsenmeister",
        username: "@pichsenmeister"
    }
}</pre>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h3 id="obtain-user-information-from-access-token">5. Obtain user information from access token</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>
                    Once a valid access token is generated and the user haven't revoked access to your app,
                    user information can be retrieved via the <code>https://telegramlogin.com/user</code> endpoint.
                    Therefore the access token must be given as <code>access_token</code> parameter.
                    The request can be either <code>GET</code> or <code>POST</code>.
                </p>
                <p>
                    <i>Example <code>POST</code> request:</i>
                    <br>
                    <pre>POST /user
Host: telegramlogin.com
Content-Type: application/x-www-form-urlencoded

access_token=&lt;access_token&gt;</pre>
                </p>
                <p>
                    <i>Example <code>GET</code> request:</i>
                    <br>
                    <pre>GET /user?access_token=&lt;access_token&gt;
Host: telegramlogin.com</pre>
                </p>
                <p>
                    A successful response is the same as in <a href="#telegramlogin-user-response">Step 4</a>:
                </p>
            </div>
        </div>


    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 id="create-telegramlogin-button">Create TelegramLogin button example</h2>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <p>
                    This is an example for <a href="http://getbootstrap.com/" target="_blank">Bootstrap</a> + <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">FontAwesome</a>
                </p>
                <p>
                    <i>HTML:</i>
                    <br>
                    <code>
                        &lt;a class="btn btn-logo" href="https://telegramlogin.com/token/&lt;your_client_id&gt;?state=&lt;your_csrf_token&gt;"&gt;
                        <br>
                        &nbsp;&nbsp;&lt;i class="fa fa-paper-plane-o"&gt;&lt;/i&gt; Login with Telegram
                        <br>
                        &lt;/a&gt;
                    </code>
                </p>
                <p>
                    <i>CSS:</i>
                    <br>
                    <code>
                        .btn-logo, .btn-logo:hover {
                            background: #1e96c8;
                            border-color: #1e96c8;
                            color: #fff !important;

                        }
                        <br>
                        .btn-logo:active, .btn-logo:focus {
                            background: #057daf !important;
                            border-color: #057daf !important;
                        }
                    </code>

                </p>
                <p>
                    <i>RESULT:</i>
                    <br>
                    <a class="btn btn-logo" href="https://telegramlogin.com/token/314159265">
                        <i class="fa fa-paper-plane-o"></i> Login with Telegram
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="mtl mbl"></div>
@endsection
