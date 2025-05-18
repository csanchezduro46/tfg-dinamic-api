import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { ExecutionModalComponent } from '../execution-modal/execution-modal.component';

@Component({
  selector: 'app-executions-page',
  imports: [CommonModule, FontAwesomeModule, FormsModule, ExecutionModalComponent],
  templateUrl: './executions-page.component.html',
  styleUrl: './executions-page.component.css'
})
export class ExecutionsPageComponent implements OnInit {
  executions: any = [];
  loading = false;

  showExecuteModal = false;
  executionLoading = false;
  mappings: any[] = [];
  selectedMappingId: number | null = null;

  constructor(private readonly executionsService: ExecutionService, library: FaIconLibrary,
    private readonly mappingService: ApiCallMappingService, private readonly globalSuccessService: GlobalSuccessService) {
    library.addIconPacks(fal, fas);
  }

  ngOnInit(): void {
    this.mappingService.getAll().subscribe(m => (this.mappings = m));
    this.fetchExecutions();
  }

  fetchExecutions(): void {
    this.loading = true;
    this.executionsService.getAll()
      .subscribe({
        next: (data) => {
          this.executions = data;
          this.loading = false;
        },
        error: () => {
          this.loading = false;
          alert('Error al cargar las ejecuciones');
        }
      });
  }

  openExecutionModal(): void {
    this.selectedMappingId = null;
    this.showExecuteModal = true;
  }

  runExecution(): void {
    this.executionLoading = true;
  }

  finishExecution(): void {
    this.executionLoading = false;
  }

  openLog(execution: any) {
    const logs = execution.response_log;
    console.log(logs)
  }

  repeat(execution: any) {
    this.selectedMappingId = execution.api_call_mapping_id;
    this.runExecution();
    this.selectedMappingId = null;
  }
}
