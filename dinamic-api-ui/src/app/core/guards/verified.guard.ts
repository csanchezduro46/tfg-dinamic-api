import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

export const verifiedGuard: CanActivateFn = () => {
    const router = inject(Router);
    const userRaw = localStorage.getItem('user');

    if (!userRaw) return router.parseUrl('/login');

    const user = JSON.parse(userRaw);
    return user.email_verified_at ? true : router.parseUrl('/verify-email');
};
