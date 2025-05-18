import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
import { GlobalInfoService } from '../../../shared/services/generic/global-info.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';
import { ScheduledExecutionModalComponent } from '../scheduled-execution-modal/scheduled-execution-modal.component';

@Component({
  selector: 'app-scheduled-executions-page',
  imports: [CommonModule, FontAwesomeModule, ScheduledExecutionModalComponent, ConfirmPopupComponent],
  standalone: true,
  templateUrl: './scheduled-executions-page.component.html',
  styleUrl: './scheduled-executions-page.component.css'
})
export class ScheduledExecutionsPageComponent implements OnInit {
  scheduled: any[] = [];
  loading = false;
  showScheduledModal: boolean = false;
  confirmDeleteId!: number | null;
  mappings: any[] = [];
  editing!: any;
  executionLoading: boolean = false;

  constructor(private readonly executionService: ExecutionService, library: FaIconLibrary,
    private readonly globalSuccessService: GlobalSuccessService,
    private readonly mappingService: ApiCallMappingService, private readonly globalInfoServive: GlobalInfoService) {
    library.addIconPacks(fas)
  }

  ngOnInit(): void {
    this.loading = true;
    this.mappingService.getAll().subscribe(m => this.mappings = m);
    this.fetchScheduled();
  }

  fetchScheduled() {
    this.showScheduledModal = false;
    this.editing = null;
    this.executionService.getScheduled().subscribe({
      next: (data) => {
        this.scheduled = data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
  }

  onNew() {
    this.showScheduledModal = !this.showScheduledModal;
  }

  onEdit(connection: any) {
    this.editing = connection;
    this.showScheduledModal = !this.showScheduledModal;
  }

  runNow(mappingId: number): void {
    this.executionService.create(mappingId, { execution_type: 'manual' }).subscribe(() => {
      this.globalSuccessService.show('La sincronización se ha realizado correctamente.', 'Sincronización correcta');
      this.fetchScheduled();
    });
  }

  askDelete(id: number): void {
    this.confirmDeleteId = id;
  }

  confirmDelete(): void {
    if (this.confirmDeleteId) {
      this.executionService.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.scheduled = this.scheduled.filter(e => e.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetchScheduled();
        },
        error: () => this.confirmDeleteId = null
      });
    }
  }

  launch(execution: any) {
    this.executionLoading = true;
    this.executionService.launch(execution.id).subscribe({
      next: () => {
        this.globalSuccessService.show('La ejecución se ha realizado correctamente.', 'Sincronización completa');
        this.executionLoading = false;
        this.fetchScheduled();
      }, error: () => {
        this.executionLoading = false
        this.fetchScheduled();
      }
    });
  }

  openLog(execution: any) {
    const logs = execution.response_log;
    if(logs) {
      this.globalInfoServive.show(logs,'Mensajes de respuesta','info')
    }
  }
}
