import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavigationEnd, Router, RouterLinkWithHref, RouterModule, RouterOutlet, TitleStrategy } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { GenericErrorPopupComponent } from "../ui/generic-error-popup/generic-error-popup.component";
import { AuthService } from '../services/oauth/auth.service';
import { User } from '../../core/models/user.model';
import { filter, map, Subscriber } from 'rxjs';
import { NavGroup, NAV_ITEMS } from './nav-items';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ConfirmPopupComponent } from '../ui/confirm-popup/confirm-popup.component';
import { GenericSuccessPopupComponent } from '../ui/generic-success-popup/generic-success-popup.component';

@Component({
  standalone: true,
  selector: 'app-layout',
  imports: [RouterOutlet, CommonModule, FontAwesomeModule, RouterLinkWithHref, GenericErrorPopupComponent, RouterModule,
    ConfirmPopupComponent, GenericSuccessPopupComponent],
  templateUrl: './layout.component.html',
  styleUrl: './layout.component.css'
})
export class LayoutComponent {
  pageTitle = 'Panel de control';
  user!: User | null;
  subscriberUser!: any;
  navItems: NavGroup[] = NAV_ITEMS;
  showMenu = false;
  confirmLogout = false;

  constructor(private readonly router: Router, private readonly faLibrary: FaIconLibrary,
    private readonly auth: AuthService, private readonly titleStrategy: TitleStrategy) {
    this.subscriberUser = this.auth.user$.subscribe({
      next: user => {
        this.user = user;
      }
    });
    this.faLibrary.addIconPacks(fal, fas);
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd),
      map(() => {
        let route = this.router.routerState.root;
        let child = route.firstChild;

        while (child?.firstChild) {
          child = child.firstChild;
        }
        let title = this.titleStrategy.getResolvedTitleForRoute(child?.snapshot ?? route.snapshot);
        return title || 'Panel de control';
      })
    ).subscribe((title: string) => {
      this.pageTitle = title;
    });
  }

  // Mostramos el diálogo de confirmación
  logout(): void {
    this.showMenu = !this.showMenu;
    this.confirmLogout = true;
  }

  // Acción confirmada
  confirmLogoutAction(): void {
    this.confirmLogout = false;
    this.auth.logout();
    this.router.navigate(['/login']);
  }
}
