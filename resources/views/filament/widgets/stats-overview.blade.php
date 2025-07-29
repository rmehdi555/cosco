<div class="w-full bg-white rounded-lg shadow-xl border border-gray-200 p-6 mb-6" style="z-index: 999; position: relative;">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-200 to-blue-400 text-blue-900 rounded-lg p-6 flex flex-col items-center shadow-lg hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center space-x-3 mb-3 rtl:space-x-reverse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A8.966 8.966 0 0112 15c1.657 0 3.182.502 4.41 1.355M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="text-4xl font-extrabold">{{ $usersCount ?? 0 }}</div>
            </div>
            <div class="text-lg font-semibold text-blue-800 tracking-wide">تعداد کاربران</div>
        </div>

        <div class="bg-gradient-to-br from-green-200 to-green-400 text-green-900 rounded-lg p-6 flex flex-col items-center shadow-lg hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center space-x-3 mb-3 rtl:space-x-reverse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 00-3-3.87M15 17v-2a4 4 0 013-3.87M12 12v5m-3-4a3 3 0 116 0" />
                </svg>
                <div class="text-4xl font-extrabold">{{ $ordersCount ?? 0 }}</div>
            </div>
            <div class="text-lg font-semibold text-green-800 tracking-wide">سفارش‌ها تعداد</div>
        </div>

        <div class="bg-gradient-to-br from-yellow-200 to-yellow-400 text-yellow-900 rounded-lg p-6 flex flex-col items-center shadow-lg hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center space-x-3 mb-3 rtl:space-x-reverse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2h-3.586a1 1 0 01-.707-.293l-4.414-4.414A2 2 0 008 0H6a2 2 0 00-2 2v8a2 2 0 002 2h3.586a1 1 0 01.707.293l4.414 4.414A2 2 0 0014 16h2a2 2 0 002-2v-3z" />
                </svg>
                <div class="text-4xl font-extrabold">{{ $productsCount ?? 0 }}</div>
            </div>
            <div class="text-lg font-semibold text-yellow-800 tracking-wide">محصولات تعداد</div>
        </div>

        <div class="bg-gradient-to-br from-purple-200 to-purple-400 text-purple-900 rounded-lg p-6 flex flex-col items-center shadow-lg hover:shadow-2xl transition-shadow duration-300">
            <div class="flex items-center space-x-3 mb-3 rtl:space-x-reverse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                </svg>
                <div class="text-4xl font-extrabold">{{ $articlesCount ?? 0 }}</div>
            </div>
            <div class="text-lg font-semibold text-purple-800 tracking-wide">مقالات تعداد</div>
        </div>
    </div>
</div>
