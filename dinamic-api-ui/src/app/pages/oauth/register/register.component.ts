import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faEye } from '@fortawesome/pro-light-svg-icons';
import { GenericErrorPopupComponent } from '../../../shared/ui/generic-error-popup/generic-error-popup.component';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { GenericSuccessPopupComponent } from '../../../shared/ui/generic-success-popup/generic-success-popup.component';
import { GlobalErrorService } from '../../../shared/services/errors/global-error.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule, FormsModule, FontAwesomeModule, GenericErrorPopupComponent,
    GenericSuccessPopupComponent],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  error = '';
  success = '';
  showVerify = false;
  showPassword = false;
  submitted = false;
  form: FormGroup;

  constructor(private readonly auth: AuthService, private readonly router: Router,
    private readonly fb: FormBuilder, library: FaIconLibrary, private readonly globalSuccessService: GlobalSuccessService) {
    this.form = this.fb.group({
      name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required, Validators.minLength(8)]],
      terms: [false, [Validators.required, Validators.requiredTrue]]
    });
    library.addIcons(faEye);
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  toggleVerify() {
    this.showVerify = !this.showVerify;
  }

  submit() {    
    if (!this.form.value.terms) {
      this.error = 'Debes aceptar los términos y condiciones.';
      return;
    }

    this.submitted = true;

    if (this.form.value.password !== this.form.value.password_confirmation) {
      this.error = 'Las contraseñas no coinciden.';
      return;
    }

    this.auth.register(this.form.value).subscribe({
      next: (res) => {
        this.error = '';
        this.globalSuccessService.show('La cuenta se ha creado correctamente. Por favor, verifica tu correo electrónico para poder iniciar sesión.', 'Verifica tu correo electrónico');
        this.router.navigate(['/login']);
      },
      error: (err) => {
        this.error = err?.error?.msg || 'No se pudo completar el registro.';
        this.success = '';
      }
    });
  }

  isInvalid(controlName: string): boolean {
    const control = this.form.get(controlName);
    return control ? control.invalid && (control.touched || this.submitted) : false;
  }
}
