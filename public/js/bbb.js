(function () {
    $(document).ready(function () {
        $("#Formlogin").on("submit", function (e) {
            e.preventDefault();

            const email = $("#email").val();
            const password = $("#password").val();

            if (!email || !password) {
                alert("Email dan password harus diisi");
                return;
            }

            loginUser(email, password);
        });
    });

    function loginUser(email, password) {
        $.ajax({
            url: "/postlogin",
            type: "POST",
            data: {
                email: email,
                password: password,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                alert("Login berhasil!");
                window.location.href = "/";
            },
            error: function (xhr) {
                let errorMessage = "Login gagal";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 401) {
                    errorMessage = "Email atau password salah";
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join(", ");
                    } else {
                        errorMessage = "Data tidak valid";
                    }
                } else if (xhr.status === 419) {
                    errorMessage = "Session expired, silakan refresh halaman";
                } else if (xhr.status === 500) {
                    errorMessage = "Server error, silakan coba lagi";
                }

                alert(errorMessage);
            },
        });
    }
})();
