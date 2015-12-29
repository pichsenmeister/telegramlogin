<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

    </head>
    <body>
        <div class="container">
            <div class="content">
                @foreach($errors as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        </div>
    </body>
</html>
