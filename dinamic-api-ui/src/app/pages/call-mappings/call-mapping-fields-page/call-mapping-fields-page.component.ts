import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ApiCallMappingFieldService } from '../../../shared/services/api/api-call-mapping-field.service';

@Component({
  selector: 'app-call-mapping-fields-page',
  imports: [CommonModule, FontAwesomeModule],
  standalone: true,
  templateUrl: './call-mapping-fields-page.component.html',
  styleUrl: './call-mapping-fields-page.component.css'
})
export class CallMappingFieldsPageComponent implements OnInit {
  fields: any[] = [];
  loading = false;
  mappingId!: number;

  constructor(private readonly fieldsService: ApiCallMappingFieldService, private readonly route: ActivatedRoute,
    library: FaIconLibrary) {
    library.addIconPacks(fas)
  }

  ngOnInit(): void {
    this.mappingId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.mappingId) {
      this.fetchFields(this.mappingId);
    }
  }

  fetchFields(id: number): void {
    this.loading = true;
    this.fieldsService.getByMapping(id).subscribe({
      next: (data) => {
        this.fields = data;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        alert('Error al cargar los campos del mapeo');
      }
    });
  }

  onDelete(id: number): void {
    if (!confirm('¿Eliminar este campo mapeado?')) return;
    this.fieldsService.delete(this.mappingId, id).subscribe({
      next: () => {
        this.fields = this.fields.filter(f => f.id !== id);
      },
      error: () => alert('Error al eliminar')
    });
  }
}