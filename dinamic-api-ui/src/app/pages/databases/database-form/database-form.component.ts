import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { User } from '../../../core/models/user.model';
import { DatabaseConnectionService } from '../../../shared/services/api/database-connection.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';

@Component({
  selector: 'app-database-form',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  standalone: true,
  templateUrl: './database-form.component.html',
  styleUrl: './database-form.component.css'
})
export class DatabaseFormComponent implements OnInit {
  @Input() connection: any = null;
  @Output() cancel = new EventEmitter<void>();
  @Output() submit = new EventEmitter<void>();

  form!: FormGroup;
  user!: User;
  admin: boolean = false;

  constructor(private readonly fb: FormBuilder, private readonly auth: AuthService,
    private readonly dbService: DatabaseConnectionService) { }

  ngOnInit(): void {
    this.admin = this.auth.hasRole('admin');
    this.form = this.fb.group({
      user_id: [this.auth.getUser().id || null, Validators.required],
      name: [this.connection?.name || '', Validators.required],
      driver: [this.connection?.driver || 'mysql', Validators.required],
      host: [this.connection?.host || '', Validators.required],
      port: [this.connection?.port || 3306, Validators.required],
      database: [this.connection?.database || '', Validators.required],
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  onSubmit(): void {
    if (this.connection) {
      this.form.setControl('user_id', null);
    }
    if (this.form.invalid) return;

    const data = this.form.value;

    if (this.connection?.id) {
      this.dbService.update(this.connection.id, data).subscribe({
        next: () => this.submit.emit()
      })
    } else {
      this.dbService.create(data).subscribe({
        next: () => this.submit.emit()
      })
    }
  }
}
