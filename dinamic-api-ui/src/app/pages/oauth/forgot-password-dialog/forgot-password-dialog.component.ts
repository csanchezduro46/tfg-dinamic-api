import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PasswordService } from '../../../shared/services/oauth/password.service';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faKey } from '@fortawesome/pro-light-svg-icons';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-forgot-password-dialog',
  standalone: true,
  imports: [CommonModule, FormsModule, FontAwesomeModule],
  templateUrl: './forgot-password-dialog.component.html',
})
export class ForgotPasswordDialogComponent {
  @Input() close: () => void = () => { };
  email = '';
  success = '';
  error = '';
  loading = false;

  constructor(private readonly auth: PasswordService, library: FaIconLibrary,
    private readonly globalSuccessService: GlobalSuccessService) {
    library.addIcons(faKey);
  }

  submit() {
    this.loading = true;
    this.auth.forgotPassword(this.email).subscribe({
      next: () => {
        this.success = 'Se ha enviado el email de recuperación.';
        this.error = '';
        this.loading = false;
        this.globalSuccessService.show('Se ha enviado el email de verificación.');
        this.close();
      },
      error: () => {
        this.error = 'No se pudo enviar el correo.';
        this.success = '';
        this.loading = false;
      }
    });
  }
}
