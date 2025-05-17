import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ApiCallService } from '../../../shared/services/api/api-call.service';
import { PlatformVersionService } from '../../../shared/services/api/platform-version.service';
import { GlobalErrorService } from '../../../shared/services/errors/global-error.service';

@Component({
  selector: 'app-platform-api-form',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  standalone: true,
  templateUrl: './platform-api-form.component.html',
  styleUrl: './platform-api-form.component.css'
})
export class PlatformApiFormComponent implements OnInit {
  @Input() api: any = null;
  @Input() versionId!: number;

  @Input() platforms: any[] = [];
  @Input() versions: any[] = [];
  @Input() groups: any[] = [];

  @Output() cancel = new EventEmitter<void>();
  @Output() correct = new EventEmitter<void>();

  form!: FormGroup;

  constructor(private readonly fb: FormBuilder, private readonly apisService: ApiCallService,
    private readonly versionService: PlatformVersionService, private readonly genericError: GlobalErrorService) { }

  ngOnInit(): void {
    const payload = this.api?.payload_example
    ? JSON.stringify(this.api.payload_example, null, 2)
    : '';

  const response = this.api?.response_example
    ? JSON.stringify(this.api.response_example, null, 2)
    : '';

    this.form = this.fb.group({
      platform_id: [this.api?.version.platform_id, Validators.required],
      platform_version_id: [this.api?.platform_version_id, Validators.required],
      group_id: [this.api?.group_id, Validators.required],
      name: [this.api?.name || '', Validators.required],
      endpoint: [this.api?.endpoint || '', Validators.required],
      method: [this.api?.method || 'MUTATION', Validators.required],
      request_type: [this.api?.request_type || '', Validators.required],
      response_type: [this.api?.response_type || '', Validators.required],
      description: [this.api?.description || ''],
      payload_example: [payload || '', Validators.required],
      response_example: [response || ''],
    });

    // Actualizar la version cuando cambia la plataforma seleccionada
    this.form.get('platform_id')?.valueChanges.subscribe(() => {
      this.form.get('platform_version_id')?.setValue('');
      this.filteredVersions();
    });
  }

  filteredVersions() {
    const selectedPlatformId = this.form.get('platform_id')?.value;
    this.versionService.getByPlatform(selectedPlatformId).subscribe({
      next: (versions) => this.versions = versions
    });
  }

  onSubmit(): void {
    if (this.form.invalid) return;

    const data = { ...this.form.value };

    try {
      // Validar y convertir JSON
      data.payload_example = JSON.parse(data.payload_example);
    } catch (e) {
      this.genericError.show('El campo "Payload de petición" no contiene un JSON válido.');
      return;
    }

    try {
      if (data.response_example?.trim()) {
        data.response_example = JSON.parse(data.response_example);
      } else {
        data.response_example = null;
      }
    } catch (e) {
      this.genericError.show('El campo "Ejemplo de respuesta" no contiene un JSON válido.');
      return;
    }

    if (this.api?.id) {
      this.apisService.update(this.api.id, data).subscribe({
        next: () => { this.correct.emit(); }
      })
    } else {
      this.apisService.create(data).subscribe({
        next: () => { this.correct.emit(); }
      })
    }
  }
}
