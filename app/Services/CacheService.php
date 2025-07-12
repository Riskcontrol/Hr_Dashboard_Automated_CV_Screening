<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Cache;

// class CacheService
// {
//     public function rememberDashboardStats($callback)
//     {
//         return Cache::store('applications')->remember('dashboard_stats', 300, $callback); // 5 minutes
//     }

//     public function rememberKeywordSets($callback)
//     {
//         return Cache::store('applications')->remember('active_keyword_sets', 600, $callback); // 10 minutes
//     }

//     public function forgetDashboardStats()
//     {
//         Cache::store('applications')->forget('dashboard_stats');
//     }

//     public function forgetKeywordSets()
//     {
//         Cache::store('applications')->forget('active_keyword_sets');
//     }
// }