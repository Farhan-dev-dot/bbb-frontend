(function () {
    $(document).ready(function () {
        $("#Formlogin").on("submit", function (e) {
            e.preventDefault();

            const username = $("#username").val();
            const password = $("#password").val();

            if (!username || !password) {
                alert("username dan password harus diisi");
                return;
            }

            loginUser(username, password);
        });
    });

    function loginUser(username, password) {
        $.ajax({
            url: "/postlogin",
            type: "POST",
            data: {
                username: username,
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
                    errorMessage = "username atau password salah";
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
