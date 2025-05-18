import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
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

  constructor(private readonly executionService: ExecutionService, library: FaIconLibrary,
    private readonly globalSuccessService: GlobalSuccessService,
    private readonly mappingService: ApiCallMappingService) {
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
}
