@if(session()->has('success'))
    <script>
        Swal.fire(
            'Success!',
            '{{ session()->get('success') }}',
            'success'
        )
    </script>
@endif

@if(session()->has('error'))
    <script>
        Swal.fire(
            'Error!',
            '{{ session()->get('error') }}',
            'error'
        )
    </script>
@endif

@if(session()->has('info'))
    <script>
        Swal.fire(
            'Info!',
            '{{ session()->get('info') }}',
            'info'
        )
    </script>
@endif

@if(session()->has('warning'))
    <script>
        Swal.fire(
            'Warning!',
            '{{ session()->get('warning') }}',
            'warning'
        )
    </script>
@endif

@if($errors->any())
    @foreach($errors->all() as $error)
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'error',
                title: '{{ $error }}'
            });
            /*Swal.fire(
                'Error!',
                '{{ $error }}',
                'error'
            )*/
        </script>
    @endforeach
@endif

