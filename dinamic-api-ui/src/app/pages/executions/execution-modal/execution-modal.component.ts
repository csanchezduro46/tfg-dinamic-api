import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-execution-modal',
  standalone: true,
  templateUrl: './execution-modal.component.html',
  styleUrl: './execution-modal.component.css',
  imports: [FormsModule, CommonModule]
})
export class ExecutionModalComponent implements OnInit {
  @Input() visible = false;
  @Input() mappings: any[] = [];

  @Output() close = new EventEmitter<void>();
  @Output() executed = new EventEmitter<void>();
  @Output() finish = new EventEmitter<void>();

  selectedMappingId: number | null = null;

  constructor(
    private readonly executionService: ExecutionService,
    private readonly globalSuccessService: GlobalSuccessService
  ) { }

  ngOnInit(): void { }

  run(): void {
    if (!this.selectedMappingId) return;
    this.executed.emit();

    this.executionService.create(this.selectedMappingId, { execution_type: 'manual' }).subscribe({
      next: () => {
        this.finish.emit();
        this.close.emit();
        this.globalSuccessService.show('La sincronización se ha realizado correctamente.', 'Sincronización correcta');
      },
      error: () => {
        this.finish.emit();
      }
    });
  }
}
