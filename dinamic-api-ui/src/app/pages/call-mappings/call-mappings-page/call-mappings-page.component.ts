import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { RouterModule } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';

@Component({
  selector: 'app-call-mappings-page',
  imports: [CommonModule, FontAwesomeModule, RouterModule, ConfirmPopupComponent],
  templateUrl: './call-mappings-page.component.html',
  styleUrl: './call-mappings-page.component.css'
})
export class CallMappingsPageComponent implements OnInit {
  mappings: any[] = [];
  loading = false;
  admin: boolean = false;
  loadingMapping: boolean = false;

  confirmDeleteId!: number|null;

  constructor(private readonly apiCallMappings: ApiCallMappingService, library: FaIconLibrary,
    private readonly auth: AuthService,
    private readonly executionService: ExecutionService,
    private readonly globalSuccessService: GlobalSuccessService) {
    library.addIconPacks(fal, fas);
  }

  ngOnInit(): void {
    this.admin = this.auth.hasRole('admin');
    this.fetchMappings();
  }

  fetchMappings(): void {
    this.loading = true;
    this.apiCallMappings.getAll()
      .subscribe({
        next: (data) => {
          this.mappings = data;
          this.loading = false;
        },
        error: () => {
          this.loading = false;
          alert('Error al cargar los mapeos');
        }
      });
  }

  askDelete(id: number): void {
    this.confirmDeleteId = id;
  }

  confirmDelete(): void {
    if (this.confirmDeleteId) {
      this.apiCallMappings.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.mappings = this.mappings.filter(p => p.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetchMappings();
        }
      });
    }
  }

  onNew() {

  }

  launchMapping(mapping: any) {
    this.loadingMapping = true;
    this.executionService.launch(mapping.id).subscribe({
      next: () => {
        this.globalSuccessService.show('La sincronización se ha realizado correctamente.', 'Sincronización completa');
        this.loadingMapping = false;
      }, error: () => this.loadingMapping = false
    });
  }
}
