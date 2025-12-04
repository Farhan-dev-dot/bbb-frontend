/**
 * Session Manager untuk menangani autentikasi token
 * Mengelola auto-logout saat session expired
 */
class SessionManager {
    constructor() {
        this.checkInterval = 60000; // Check setiap 1 menit
        this.isChecking = false;
        this.init();
    }

    init() {
        // Start session checking
        this.startSessionCheck();

        // Handle AJAX errors globally untuk 401 responses
        this.setupAjaxErrorHandler();

        // Handle visibility change untuk check session saat tab aktif kembali
        document.addEventListener("visibilitychange", () => {
            if (!document.hidden) {
                this.checkSession();
            }
        });
    }

    startSessionCheck() {
        setInterval(() => {
            this.checkSession();
        }, this.checkInterval);
    }

    async checkSession() {
        if (this.isChecking) return;

        this.isChecking = true;

        try {
            const response = await fetch("/check-session", {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            const data = await response.json();

            if (!response.ok || !data.authenticated) {
                this.handleSessionExpired();
            }
        } catch (error) {
            console.error("Session check failed:", error);
        } finally {
            this.isChecking = false;
        }
    }

    setupAjaxErrorHandler() {
        // Handle untuk jQuery AJAX jika digunakan
        if (window.$ && $.ajaxSetup) {
            $(document).ajaxError((event, xhr, settings) => {
                if (xhr.status === 401) {
                    this.handleSessionExpired();
                }
            });
        }

        // Handle untuk Axios jika digunakan
        if (window.axios) {
            axios.interceptors.response.use(
                (response) => response,
                (error) => {
                    if (error.response && error.response.status === 401) {
                        this.handleSessionExpired();
                    }
                    return Promise.reject(error);
                }
            );
        }
    }

    handleSessionExpired() {
        // Show notification
        this.showSessionExpiredNotification();

        // Redirect ke login setelah delay singkat
        setTimeout(() => {
            window.location.href = "/login";
        }, 3000);
    }

    showSessionExpiredNotification() {
        // Cek apakah SweetAlert2 tersedia
        if (window.Swal) {
            Swal.fire({
                icon: "warning",
                title: "Session Expired",
                text: "Sesi Anda telah berakhir. Anda akan diarahkan ke halaman login.",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }
        // Fallback ke alert biasa
        else {
            alert(
                "Sesi Anda telah berakhir. Anda akan diarahkan ke halaman login."
            );
        }
    }

    // Method untuk manual logout
    logout() {
        window.location.href = "/logout";
    }

    // Method untuk refresh token (jika API mendukung)
    async refreshToken() {
        try {
            const response = await fetch("/refresh-token", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            return response.ok;
        } catch (error) {
            console.error("Token refresh failed:", error);
            return false;
        }
    }
}

// Initialize session manager saat DOM ready
document.addEventListener("DOMContentLoaded", function () {
    // Jangan jalankan di halaman login
    if (!window.location.pathname.includes("/login")) {
        window.sessionManager = new SessionManager();
    }
});

// Export untuk penggunaan sebagai module jika diperlukan
if (typeof module !== "undefined" && module.exports) {
    module.exports = SessionManager;
}
