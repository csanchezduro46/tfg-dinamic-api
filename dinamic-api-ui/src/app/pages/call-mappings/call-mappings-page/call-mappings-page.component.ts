import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';

@Component({
  selector: 'app-call-mappings-page',
  imports: [CommonModule, FontAwesomeModule],
  templateUrl: './call-mappings-page.component.html',
  styleUrl: './call-mappings-page.component.css'
})
export class CallMappingsPageComponent implements OnInit {
  mappings: any[] = [];
  loading = false;

  constructor(private readonly apiCallMappings: ApiCallMappingService, library: FaIconLibrary) {
    library.addIconPacks(fal, fas);
  }

  ngOnInit(): void {
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

  onDelete(id: number): void {
    if (!confirm('¿Eliminar este mapeo?')) return;

    this.apiCallMappings.delete(id)
      .subscribe({
        next: () => {
          this.mappings = this.mappings.filter(m => m.id !== id);
        },
        error: () => alert('Error al eliminar el mapeo')
      });
  }
}
