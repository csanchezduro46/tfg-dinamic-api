import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { PlatformService } from '../../../shared/services/api/platform.service';

@Component({
  selector: 'app-platform-form',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  standalone: true,
  templateUrl: './platform-form.component.html',
  styleUrl: './platform-form.component.css'
})
export class PlatformFormComponent implements OnInit {
  @Input() platform: any = null;
  @Output() cancel = new EventEmitter<void>();
  @Output() submit = new EventEmitter<void>();

  form!: FormGroup;

  constructor(private readonly fb: FormBuilder, private readonly platformService: PlatformService) { }

  ngOnInit(): void {
    this.form = this.fb.group({
      name: [this.platform?.name || '', Validators.required],
      slug: [this.platform?.slug || '', [Validators.required, Validators.pattern(/^[a-z0-9-]+$/)]]
    });
  }

  onSubmit(): void {
    if (this.form.invalid) return;

    const data = this.form.value;

    if (this.platform?.id) {
      this.platformService.update(this.platform.id, data).subscribe({
        next: () => { this.submit.emit(); }
      });
    } else {
      this.platformService.create(data).subscribe({
        next: () => { this.submit.emit(); }
      });
    }
  }
}
