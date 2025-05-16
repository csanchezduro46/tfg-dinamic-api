import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavigationEnd, Router, RouterLinkWithHref, RouterOutlet } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { GenericErrorPopupComponent } from "../ui/generic-error-popup/generic-error-popup.component";
import { AuthService } from '../services/oauth/auth.service';
import { User } from '../../core/models/user.model';
import { Subscriber } from 'rxjs';

@Component({
  standalone: true,
  selector: 'app-layout',
  imports: [RouterOutlet, CommonModule, FontAwesomeModule, RouterLinkWithHref, GenericErrorPopupComponent],
  templateUrl: './layout.component.html',
  styleUrl: './layout.component.css'
})
export class LayoutComponent {
  pageTitle = 'Panel';
  user!: User | null;
  subscriberUser!: any;

  constructor(private readonly router: Router, private readonly faLibrary: FaIconLibrary,
    private readonly auth: AuthService) {
    this.subscriberUser = this.auth.user$.subscribe({ next: user => {
      this.user = user;
    }
    });
    this.faLibrary.addIconPacks(fal);

    this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd) {
        let route = this.router.routerState.root;
        while (route.firstChild) {
          route = route.firstChild;
        }

        const title = route.snapshot.data['title'];
        this.pageTitle = title || 'Panel';
      }
    });
  }
}
