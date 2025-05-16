import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { PasswordService } from '../../../shared/services/oauth/password.service';
import { CommonModule } from '@angular/common';
import { GenericErrorPopupComponent } from '../../../shared/ui/generic-error-popup/generic-error-popup.component';

@Component({
  selector: 'app-reset-password',
  imports: [CommonModule, FormsModule, ReactiveFormsModule, RouterModule, GenericErrorPopupComponent],
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
    private readonly passwordService: PasswordService
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
        next: (res: any) => this.msg = 'Contraseña restablecida correctamente.',
        error: (err) => this.msg = 'Error al restablecer contraseña.'
      });
  }
}
