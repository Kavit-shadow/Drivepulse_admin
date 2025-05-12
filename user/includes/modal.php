 <!-- Add custom modal HTML before the script tags -->
   <!-- Custom Logout Modal -->
   <div id="logoutModal" class="fixed inset-0 z-50 hidden">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm"></div>
      
      <!-- Modal -->
      <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
         <div class="p-6">
            <!-- Icon -->
            <div class="mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
               <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
               </svg>
            </div>
            
            <!-- Content -->
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">
               Logout Confirmation
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
               Are you sure you want to logout?
            </p>
            
            <!-- Buttons -->
            <div class="flex space-x-3">
               <button id="cancelLogout" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                  Cancel
               </button>
               <button id="confirmLogout" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 rounded-lg transition-colors">
                  Logout
               </button>
            </div>
         </div>
      </div>
   </div>

   <!-- Loading Modal -->
   <div id="loadingModal" class="fixed inset-0 z-50 hidden">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm"></div>
      
      <!-- Modal -->
      <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
         <div class="p-6">
            <!-- Loading Spinner -->
            <div class="flex items-center justify-center mb-4">
               <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 dark:border-gray-600 border-t-blue-600 dark:border-t-blue-400"></div>
            </div>
            
            <!-- Content -->
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">
               Logging out...
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-center">
               Please wait
            </p>
         </div>
      </div>
   </div>