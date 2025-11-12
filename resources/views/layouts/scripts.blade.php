   <!--end::App Wrapper-->
   <!--begin::Script-->
   <!--begin::Third Party Plugin(OverlayScrollbars)-->
   <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
       crossorigin="anonymous"></script>
   <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
   </script>
   <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
   <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
   <script src="{{ asset('js/adminlte.js') }}"></script>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
   <script>
       const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
       const Default = {
           scrollbarTheme: 'os-theme-light',
           scrollbarAutoHide: 'leave',
           scrollbarClickScroll: true,
       };
       document.addEventListener('DOMContentLoaded', function() {
           const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
           if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
               OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                   scrollbars: {
                       theme: Default.scrollbarTheme,
                       autoHide: Default.scrollbarAutoHide,
                       clickScroll: Default.scrollbarClickScroll,
                   },
               });
           }
       });
   </script>
   <!--end::OverlayScrollbars Configure-->
   <!-- OPTIONAL SCRIPTS -->


   <!-- sortablejs -->
   <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" crossorigin="anonymous"></script>
   <!-- sortablejs -->
   <script>
       // Check if element exists before initializing Sortable
       const sortableElement = document.querySelector('.connectedSortable');
       if (sortableElement) {
           new Sortable(sortableElement, {
               group: 'shared',
               handle: '.card-header',
           });

           const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
           cardHeaders.forEach((cardHeader) => {
               cardHeader.style.cursor = 'move';
           });
       } else {
           console.log('Sortable element not found, skipping initialization');
       }
   </script>
   <!-- apexcharts -->
   <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
       integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>
   <!-- ChartJS -->
   <script>
       // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
       // IT'S ALL JUST JUNK FOR DEMO
       // ++++++++++++++++++++++++++++++++++++++++++
       function checkSession() {
           console.log('=== CHECKING SESSION ===');

           // Skip if already on login page
           if (window.location.pathname === '/login') {
               console.log('Already on login page, skipping session check');
               return true; // Return true to indicate no redirect needed
           }

           return $.ajax({
               url: '{{ route('check-session') }}',
               type: 'GET',
               dataType: 'json',
               timeout: 10000,
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                   'Accept': 'application/json'
               },
               success: function(response) {
                   //    console.log('=== SESSION SUCCESS ===');
                   //    console.log('Authenticated:', response.authenticated);
                   //    console.log('Token exists:', !!response.token);
                   //    console.log('Token:', response.token ? response.token.substring(0, 20) + '...' : 'null');
                   //    console.log('=======================');

                   // Don't handle dashboard content here - that's handled by caller
                   if (!response.authenticated || !response.token) {
                       console.log('Session invalid - will redirect to login');
                       redirectToLogin('Session not found. Please login again.');
                       return false;
                   }

                   return response; // Return response for caller to handle
               },
               error: function(xhr, status, error) {
                   console.log('=== SESSION ERROR ===');
                   console.log('Status:', xhr.status);
                   console.log('Status Text:', xhr.statusText);
                   console.log('Error:', error);
                   console.log('Response:', xhr.responseText);
                   console.log('====================');

                   // Handle different error scenarios
                   if (xhr.status === 419) {
                       console.log('CSRF token mismatch - reloading page');
                       window.location.reload();
                   } else if (xhr.status === 401) {
                       redirectToLogin('Session expired. Please login again.');
                   } else if (xhr.status === 0) {
                       console.log('Network error or request cancelled');
                       redirectToLogin('Network error. Please check your connection.');
                   } else {
                       redirectToLogin('Connection error. Please try again.');
                   }

                   return false;
               }
           });
       }

       function updateLastUpdated() {
           const now = new Date();
           const timeString = now.toLocaleTimeString();
           $('#last-updated').text('Updated at ' + timeString);
           console.log('Last updated time set to:', timeString);
       }

       function logout() {
           console.log('=== LOGOUT PROCESS ===');

           if (confirm('Are you sure you want to logout?')) {
               console.log('Logout confirmed, redirecting...');
               window.location.href = '{{ route('home.logout') }}';
           } else {
               console.log('Logout cancelled');
           }
       }

       function redirectToLogin(message = 'Please login to access dashboard') {
           //    console.log('=== REDIRECTING TO LOGIN ===');
           //    console.log('Message:', message);
           //    console.log('============================');

           // Don't show alert if already redirecting
           if (!window.location.pathname.includes('/login')) {
               alert(message);
           }

           window.location.href = '{{ route('login') }}';
       }
   </script>
   <!-- jsvectormap -->
   <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
       integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
   <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
       integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script>
   <!--end::Script-->
   @stack('scripts')
   <!--end::Script-->

   </html>
