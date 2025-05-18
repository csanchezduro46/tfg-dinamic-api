import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faCheck } from '@fortawesome/pro-light-svg-icons';
import { VerificationService } from '../../../shared/services/oauth/verification.service';
import { GlobalErrorService } from '../../../shared/services/errors/global-error.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-resend-verify-email',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, FontAwesomeModule],
  templateUrl: './resend-verify-email.component.html',
  styleUrl: './resend-verify-email.component.css'
})
export class ResendVerifyEmailComponent {
  @Input() close: () => void = () => { };
  loading = false;
  success: string = '';
  error: string = '';
  showForgot = false;
  showVerify = false;
  submitted = false;
  form: FormGroup;

  constructor(private readonly verifyService: VerificationService, library: FaIconLibrary,
    private readonly fb: FormBuilder, private readonly globalErrorService: GlobalErrorService,
    private readonly globalSuccessService: GlobalSuccessService) {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
    });
    library.addIcons(faCheck); // aquí registras los íconos que usarás
  }

  submit() {
    this.submitted = true;

    if (this.form.invalid) {
      this.globalErrorService.show('Comprueba todos los campos por favor.')
      return;
    }

    this.loading = true;
    this.verifyService.sendEmailVerification(this.form.value.email).subscribe({
      next: () => {
        this.success = 'Se ha enviado el email de verificación.';
        this.error = '';
        this.loading = false;
        this.globalSuccessService.show('Se ha enviado el email de verificación.');
        this.close();
      },
      error: () => {
        this.success = '';
        this.loading = false;
      }
    });
  }

  isInvalid(controlName: string): boolean {
    const control = this.form.get(controlName);
    return control ? control.invalid && (control.touched || this.submitted) : false;
  }
}
