import { Component } from '@angular/core';
import { Router, RouterOutlet } from '@angular/router';
import { AuthService } from './shared/services/oauth/auth.service';
// Fetches from `http://my-prod-url` in production, `http://my-dev-url` in development.

@Component({
  standalone: true,
  selector: 'app-root',
  imports: [RouterOutlet],
  template: `<router-outlet></router-outlet>`,
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'Dinamic API';

  constructor(private readonly auth: AuthService, private readonly router: Router) { }

  ngOnInit() {
    if (localStorage.getItem('token')) {
      this.auth.getMe().subscribe({
        next: (user) => {
          this.auth.setUser(user)
        },
        error: () => {
          console.log('err')
          this.auth.logout();
          this.router.navigate(['/login']);
        }
      });
    }
  }
}
