import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { environment } from './../environments/environment';
// Fetches from `http://my-prod-url` in production, `http://my-dev-url` in development.
fetch(environment.apiUrl);
@Component({
  standalone: true,
  selector: 'app-root',
  imports: [RouterOutlet],
  template: `<router-outlet></router-outlet>`,
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'dinamic-api-ui';
}
