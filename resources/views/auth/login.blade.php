@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="login-box">
    <div class="login-logo">
        <a href="{{route('index')}}"><b>Colegio de Ingenieros del Perú - CD Junín</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Ingrese sus datos para continuar.</p>

        <form id="frmLogin" method="POST">
            @csrf
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Número cip" name="cip" maxlength="10">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Contraseña" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-info btn-block btn-flat">Iniciar</button>
                </div>
            </div>
            <div class="social-auth-links text-center">
                <a href="{{route('identify.index')}}" class="btn btn-block btn-flat"> ¿Olvido su contraseña?</a>
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<script src="{{ asset('js/tools.js') }}"></script>
<script>
    let tools = new Tools();
    let isProccess = false;
    document.addEventListener('DOMContentLoaded', function() {

        let form = document.getElementById('frmLogin');
        form.elements['cip'].focus();

        form.elements['cip'].addEventListener('keypress', function() {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b')) {
                event.preventDefault();
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            if (isProccess) return;

            if (form.elements['cip'].value.trim().length == 0) {
                tools.AlertWarning('', 'Ingrese su n° cip.');
                form.elements['cip'].focus();
            } else if (form.elements['password'].value.trim().length == 0) {
                tools.AlertWarning('', 'Ingrese su contraseña por favor.');
                form.elements['password'].focus();
            } else {
                tools.ModalAlertInfo('Login', 'Procesando petición...');
                isProccess = true;
                const data = new FormData(form);
                fetch("{{ route('login.valid') }}", {
                        method: 'POST',
                        body: data
                    })
                    .then(function(response) {
                        if (response.ok) {
                            return response.json()
                        } else {
                            throw "Error de conexión, intente nuevamente.";
                        }
                    })
                    .then(function(result) {
                        if (result.estatus === 1) {
                            window.location.href = "{{ route('admin.index') }}";
                        } else {
                            tools.ModalAlertWarning('Login', result.message, function() {
                                form.elements['cip'].focus();
                            });
                            isProccess = false;
                        }
                    })
                    .catch(function(error) {
                        tools.ModalAlertError('Login', error.message);
                        isProccess = false;
                    });
            }
        });
    });
</script>
@endsection