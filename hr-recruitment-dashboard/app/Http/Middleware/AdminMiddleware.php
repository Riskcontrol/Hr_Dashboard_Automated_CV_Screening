<?php





// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Symfony\Component\HttpFoundation\Response;

// class AdminMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next): Response
//     {
//         // Check if user is authenticated
//         if (!Auth::check()) {
//             return redirect()->route('login')->with('error', 'Please login to access this area.');
//         }

//         $user = Auth::user();
        
//         // Check if user has the role column and appropriate role
//         if (!isset($user->role) || (!$user->isAdmin() && !$user->isHRManager())) {
//             abort(403, 'Access denied. Admin privileges required.');
//         }

//         return $next($request);
//     }
// }
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!$user->role === 'admin' && !$user->role === 'hr_manager') {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}