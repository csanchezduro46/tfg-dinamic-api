import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { PlatformVersionService } from '../../../shared/services/api/platform-version.service';

@Component({
  selector: 'app-platform-version-form',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  standalone: true,
  templateUrl: './platform-version-form.component.html',
  styleUrl: './platform-version-form.component.css'
})
export class PlatformVersionFormComponent implements OnInit {
  @Input() version: any = null;
  @Input() platforms: any[] = [];

  @Output() cancel = new EventEmitter<void>();
  @Output() submit = new EventEmitter<void>();

  form!: FormGroup;

  constructor(private readonly fb: FormBuilder, private readonly versionService: PlatformVersionService) { }

  ngOnInit(): void {
    this.form = this.fb.group({
      platform_id: [this.version?.platform.id, Validators.required],
      version: [this.version?.version || '', Validators.required],
      description: [this.version?.description || '']
    });
  }

  onSubmit(): void {
    if (this.form.invalid) return;

    const data = this.form.value;

    if (this.version?.id) {
      this.versionService.update(this.version.id, data).subscribe({
        next: () => {this.submit.emit();}
      })
    } else {
      this.versionService.create(data).subscribe({
        next: () => {this.submit.emit();}
      })
    }
  }
}
