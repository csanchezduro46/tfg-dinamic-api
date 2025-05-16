import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faEye } from '@fortawesome/pro-light-svg-icons';
import { GenericErrorPopupComponent } from '../../../shared/ui/generic-error-popup/generic-error-popup.component';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, FontAwesomeModule, GenericErrorPopupComponent],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  name = '';
  email = '';
  password = '';
  password_confirmation = '';
  error = '';
  success = '';
  showVerify = false;
  showPassword = false;
  terms = false;

  constructor(private readonly auth: AuthService, private readonly router: Router, library: FaIconLibrary) {
    library.addIcons(faEye); // aquí registras los íconos que usarás
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  toggleVerify() {
    this.showVerify = !this.showVerify;
  }

  submit() {
    if (!this.terms) {
      this.error = 'Debes aceptar los términos y condiciones.';
      return;
    }

    if (this.password !== this.password_confirmation) {
      this.error = 'Las contraseñas no coinciden.';
      return;
    }

    this.auth.register({
      name: this.name,
      email: this.email,
      password: this.password,
      password_confirmation: this.password_confirmation
    }).subscribe({
      next: (res) => {
        this.success = 'Registro exitoso. Revisa tu email para verificar tu cuenta.';
        this.error = '';
        localStorage.setItem('token', res.token);
        localStorage.setItem('user', JSON.stringify(res.user));

        if (!res.user.email_verified_at) {
          this.showVerify = true;
        } else {
          this.router.navigate(['/dashboard']);
        }
      },
      error: (err) => {
        this.error = err?.error?.msg || 'No se pudo completar el registro.';
        this.success = '';
      }
    });
  }
}
