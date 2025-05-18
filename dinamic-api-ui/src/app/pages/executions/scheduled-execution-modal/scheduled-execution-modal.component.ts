import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-scheduled-execution-modal',
  standalone: true,
  templateUrl: './scheduled-execution-modal.component.html',
  imports: [CommonModule, FormsModule, ReactiveFormsModule]
})
export class ScheduledExecutionModalComponent implements OnInit {
  @Input() visible = false;
  @Input() connection!: any;
  @Input() mappings: any[] = [];

  @Output() close = new EventEmitter<void>();
  @Output() created = new EventEmitter<void>();

  form!: FormGroup;

  constructor(
    private readonly fb: FormBuilder,
    private readonly executionService: ExecutionService,
    private readonly globalSuccessService: GlobalSuccessService
  ) { }

  ngOnInit(): void {
    const date = this.connection?.started_at?.split('T')[0];
    this.form = this.fb.group({
      api_call_mapping_id: [this.connection?.api_call_mapping_id ?? null, Validators.required],
      execution_type: ['scheduled'],
      started_at: [date ?? new Date(), Validators.required],
      repeat: [this.connection?.repeat ?? 'daily', Validators.required],
      cron_expression: [this.connection?.cron_expression ?? '']
    });

    this.form?.valueChanges.subscribe((value) => {
      const isCustomRepeat = this.form.get('repeat')?.value === 'custom';
      const validators = isCustomRepeat ? [Validators.required] : [];
      this.form.get('cron_expression')?.setValidators(validators);
    });
  }

  submit(): void {
    if (this.form.invalid) return;
    if (this.connection) {
      this.executionService.update(this.connection.id, this.form.value).subscribe({
        next: () => {
          this.created.emit();
          this.globalSuccessService.show('La ejecución se ha editado correctamente.', 'Sincronización correcta');
        }
      })
    } else {
      this.executionService.create(this.form.get('api_call_mapping_id')?.value, this.form.value).subscribe({
        next: () => {
          this.created.emit();
          this.globalSuccessService.show('La ejecución se ha creado correctamente.', 'Sincronización correcta');
        }
      })
    }
  }
}
