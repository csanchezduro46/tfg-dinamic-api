import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavigationEnd, Router, RouterLinkWithHref, RouterOutlet } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fal } from '@fortawesome/pro-light-svg-icons';

@Component({
  standalone: true,
  selector: 'app-layout',
  imports: [RouterOutlet, CommonModule, FontAwesomeModule, RouterLinkWithHref],
  templateUrl: './layout.component.html',
  styleUrl: './layout.component.css'
})
export class LayoutComponent {
  pageTitle = 'Panel';

  constructor(private router: Router, private faLibrary: FaIconLibrary) {
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
