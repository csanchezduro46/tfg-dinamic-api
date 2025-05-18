import { CommonModule, Location } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ApiCallMappingFieldService } from '../../../shared/services/api/api-call-mapping-field.service';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { DatabaseConnectionService } from '../../../shared/services/api/database-connection.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  selector: 'app-call-mapping-fields-page',
  standalone: true,
  templateUrl: './call-mapping-fields-page.component.html',
  styleUrl: './call-mapping-fields-page.component.css',
  imports: [CommonModule, FontAwesomeModule, FormsModule]
})
export class CallMappingFieldsPageComponent implements OnInit {
  mappingId!: number;
  loading = true;

  sourceFields: string[] = [];
  targetFields: string[] = [];

  fieldMappings: { source_field: string; target_field: string; id: number | null }[] = [];

  constructor(
    private readonly route: ActivatedRoute,
    private readonly mappingService: ApiCallMappingService,
    private readonly dbService: DatabaseConnectionService,
    private readonly fieldsService: ApiCallMappingFieldService,
    private readonly location: Location,
    private readonly globalSuccessService: GlobalSuccessService,
    private readonly router: Router,
    library: FaIconLibrary
  ) { library.addIconPacks(fas) }

  ngOnInit(): void {
    this.mappingId = +this.route.snapshot.paramMap.get('id')!;

    this.mappingService.get(this.mappingId).subscribe({
      next: (mapping) => {
        this.fieldsService.getByMapping(this.mappingId).subscribe(fields => {
          this.fieldMappings = fields.map((val: any) => ({
            source_field: val.source_field,
            target_field: val.target_field,
            id: val.id
          }));

          if (mapping.direction === 'to_api') {
            // origen es BBDD
            this.dbService.getTableColumns(mapping.source_db_connection_id, mapping.source_table)
              .subscribe(data => {
                this.sourceFields = Array.isArray(data?.columns)
                  ? data.columns.map((val: any) => val.column_name)
                  : [];
                this.loading = false;
              });

            // destino es API → usar payload_example como base
            this.targetFields = this.extractFieldsFromPayloadExample(mapping.target_api_call?.payload_example);
          } else {
            // origen es API → usar payload_example como base
            this.sourceFields = this.extractFieldsFromPayloadExample(mapping.source_api_call?.payload_example);

            // destino es BBDD
            this.dbService.getTableColumns(mapping.target_db_connection_id, mapping.target_table)
              .subscribe(data => {
                this.targetFields = Array.isArray(data?.columns)
                  ? data.columns.map((val: any) => val.column_name)
                  : [];
                this.loading = false;
              });
          }
        });
      }, error: () => this.location.back()
    });
  }

  addMapping(): void {
    this.fieldMappings.push({ source_field: '', target_field: '', id: null });
  }

  removeMapping(index: number, id: any): void {
    if (id) {
      this.fieldsService.delete(this.mappingId, id).subscribe(() => {
        this.globalSuccessService.show('Mapeo eliminado correctamente.', 'Mapeo actualizado');
      })
    }
    this.fieldMappings.splice(index, 1);
  }

  save(): void {
    this.mappingService.update(this.mappingId, { fields: this.fieldMappings }).subscribe({
      next: () => {
        this.globalSuccessService.show('Mapeo de campos guardado correctamente.', 'Mapeo correcto');
        this.router.navigate(['/connections/mappings']);
      }
    });
  }

  extractFieldsFromPayloadExample(example: any): string[] {
    if (!example || typeof example !== 'object') return [];

    const keys = Object.keys(example).filter(k => k !== 'query');
    if (keys.length === 0) return [];

    const rootKey = keys[0];
    const rootObject = example[rootKey];

    return this.extractInnerFields(rootObject);
  }



  extractInnerFields(obj: any, prefix: string = ''): string[] {
    const fields: string[] = [];

    for (const key in obj) {
      if (!obj.hasOwnProperty(key)) continue;

      const value = obj[key];
      const path = prefix ? `${prefix}.${key}` : key;

      if (value !== null && typeof value === 'object') {
        if (Array.isArray(value)) {
          if (value.length > 0 && typeof value[0] === 'object') {
            fields.push(`${path}[0]`);
            fields.push(...this.extractInnerFields(value[0], `${path}[0]`));
          } else {
            fields.push(path); // array de strings o primitivos
          }
        } else {
          fields.push(...this.extractInnerFields(value, path));
        }
      } else {
        fields.push(path);
      }
    }

    return fields;
  }




}
