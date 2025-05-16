import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ForgotPasswordDialogComponent } from '../forgot-password-dialog/forgot-password-dialog.component';
import { GenericErrorPopupComponent } from '../../../shared/ui/generic-error-popup/generic-error-popup.component';
import { GlobalErrorService } from '../../../shared/services/errors/global-error.service';
import { ResendVerifyEmailComponent } from '../resend-verify-email/resend-verify-email.component';
import { GenericSuccessPopupComponent } from '../../../shared/ui/generic-success-popup/generic-success-popup.component';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-login',
  imports: [CommonModule, FormsModule, ReactiveFormsModule, RouterModule, ForgotPasswordDialogComponent,
    ResendVerifyEmailComponent, GenericErrorPopupComponent, GenericSuccessPopupComponent],
  templateUrl: './login.component.html'
})
export class LoginComponent {
  error = '';
  showForgot = false;
  showResend = false;
  submitted = false;
  loading = false;
  form: FormGroup;

  constructor(private readonly auth: AuthService, private readonly router: Router,
    private readonly fb: FormBuilder, private readonly globalErrorService: GlobalErrorService,
    private readonly globalSuccessService: GlobalSuccessService) {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  submit() {
    this.submitted = true;
    this.loading = true;

    if (this.form.invalid) {
      this.globalErrorService.show('Comprueba todos los campos por favor.')
      return;
    }

    this.auth.login({ email: this.form.value.email, password: this.form.value.password }).subscribe({
      next: (res) => {
        this.loading = false;

        // if (!res.user.email_verified_at) {
        //   this.globalSuccessService.show('Debes verificar antes el correo electrónico para poder iniciar sesión.', 'Verifica tu cuenta de correo');
        //   return;
        // }

        localStorage.setItem('token', res.token);
        localStorage.setItem('user', JSON.stringify(res.user));
        this.router.navigate(['/dashboard']);
      },
      error: () => {
        this.loading = false;
        this.error = 'Email o contraseña incorrectos.';
      }
    });
  }

  toggleForgot() {
    this.showForgot = !this.showForgot;
  }

  toggleResend() {
    this.showResend = !this.showResend;
  }

  isInvalid(controlName: string): boolean {
    const control = this.form.get(controlName);
    return control ? control.invalid && (control.touched || this.submitted) : false;
  }
}
