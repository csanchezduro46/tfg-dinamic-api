import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
@Component({
  selector: 'app-profile',
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  standalone: true,
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.css'
})
export class ProfileComponent implements OnInit {
  form!: FormGroup;
  loading = false;
  error = '';

  constructor(private readonly fb: FormBuilder, private readonly auth: AuthService,
    private readonly globalSuccessService: GlobalSuccessService) { }

  ngOnInit(): void {
    const user = this.auth.getUser();

    this.form = this.fb.group({
      name: [user?.name || '', Validators.required],
      password: [''],
      password_confirmation: ['']
    });
  }

  save(): void {
    this.error = '';

    const { password, password_confirmation } = this.form.value;

    if (password && password !== password_confirmation) {
      this.error = 'Las contraseñas no coinciden';
      return;
    }

    this.loading = true;

    let payload = { name: this.form.value.name };
    if (password) {
      payload = this.form.value;
    }

    this.auth.update(payload).subscribe({
      next: () => {
        this.loading = false;
        this.globalSuccessService.show('Los datos del perfil se han actualizado correctamente.', 'Perfil actualizado');
      },
      error: () => {
        this.loading = false;
      }
    });
  }
}
