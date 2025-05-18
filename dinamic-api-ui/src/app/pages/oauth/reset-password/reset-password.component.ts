import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { PasswordService } from '../../../shared/services/oauth/password.service';
import { CommonModule } from '@angular/common';
import { GenericErrorPopupComponent } from '../../../shared/ui/generic-error-popup/generic-error-popup.component';
import { GenericSuccessPopupComponent } from '../../../shared/ui/generic-success-popup/generic-success-popup.component';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-reset-password',
  imports: [CommonModule, FormsModule, ReactiveFormsModule, RouterModule, GenericErrorPopupComponent,
    GenericSuccessPopupComponent],
  templateUrl: './reset-password.component.html',
  styleUrl: './reset-password.component.css'
})
export class ResetPasswordComponent implements OnInit {
  form!: FormGroup;
  token!: string;
  email!: string;
  msg: string = '';
  error!: string;

  constructor(
    private readonly fb: FormBuilder,
    private readonly route: ActivatedRoute,
    private readonly router: Router,
    private readonly passwordService: PasswordService,
    private readonly globalSuccessService: GlobalSuccessService
  ) { }

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      this.token = params['token'];
      this.email = params['email'];
    });

    this.form = this.fb.group({
      password: ['', [Validators.required, Validators.minLength(6)]],
      password_confirmation: ['', Validators.required]
    });
  }

  submit(): void {
    if (this.form.invalid) return;

    const data = {
      token: this.token,
      email: this.email,
      password: this.form.value.password,
      password_confirmation: this.form.value.password_confirmation
    };

    this.passwordService.resetPassword(data)
      .subscribe({
        next: (res: any) => {
          this.msg = 'Contraseña restablecida correctamente.';
          this.globalSuccessService.show('La contraseña se ha actualizado correctamente.', 'Contraseña actualizada');
          this.router.navigate(['/login']);
        },
        error: (err) => this.msg = 'Error al restablecer la contraseña.'
      });
  }
}
