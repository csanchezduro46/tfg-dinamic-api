import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiCallMappingService } from '../../../shared/services/api/api-call-mapping.service';
import { DatabaseConnectionService } from '../../../shared/services/api/database-connection.service';
import { ExecutionService } from '../../../shared/services/api/execution.service';
import { PlatformConnectionService } from '../../../shared/services/api/platform-connection.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';

@Component({
  selector: 'app-call-mapping-create-page',
  standalone: true,
  templateUrl: './call-mapping-create-page.component.html',
  styleUrl: './call-mapping-create-page.component.css',
  imports: [CommonModule, FormsModule, ReactiveFormsModule]
})
export class CallMappingCreatePageComponent implements OnInit {
  form!: FormGroup;

  platformConnections: any[] = [];
  dbConnections: any[] = [];
  admin: boolean = false;
  tablesDbConnection: any[] = [];
  mappingId!: number | null;

  constructor(
    private readonly fb: FormBuilder,
    private readonly platformService: PlatformConnectionService,
    private readonly dbService: DatabaseConnectionService,
    private readonly mappingService: ApiCallMappingService,
    private readonly auth: AuthService,
    private readonly globalSuccessService: GlobalSuccessService,
    private readonly router: Router,
    private readonly route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    this.admin = this.auth.hasRole('admin');

    this.form = this.fb.group({
      user_id: [this.auth.getUser().id || null, Validators.required],
      name: ['', Validators.required],
      direction: ['to_api', Validators.required], // o 'from_api'
      source_platform_connection_id: [null],
      source_db_connection_id: [null],
      source_table: [''],
      target_api_call_id: [null],
      target_db_connection_id: [null],
      target_table: [''],
      fields: [[]]
    });

    if (this.admin) {
      this.platformService.getAll().subscribe(p => this.platformConnections = p);
      this.dbService.getAll().subscribe(d => this.dbConnections = d);
    } else {
      this.platformService.getMine().subscribe(p => this.platformConnections = p);
      this.dbService.getMine().subscribe(d => this.dbConnections = d);
    }
    this.form?.valueChanges.subscribe((value) => {
      const dbFrom = this.form.get('direction')?.value === 'to_api' ? 'source_db_connection_id' : 'target_db_connection_id';
      const dbConnectionId = this.form.get(dbFrom)?.value;
      if (dbConnectionId) {
        this.dbService.getTables(dbConnectionId).subscribe(data => this.tablesDbConnection = data.tables);
      }
    });

    if (id) {
      this.mappingId = +id;
      this.getMappingEdit();
    }
  }

  getMappingEdit() {
    if (this.mappingId) {
      this.mappingService.get(this.mappingId).subscribe((mapping) => {
        this.form.patchValue({
          user_id: mapping.user_id,
          name: mapping.name,
          direction: mapping.direction,
          source_platform_connection_id: mapping.source_platform_connection_id,
          source_db_connection_id: mapping.source_db_connection_id,
          source_table: mapping.source_table,
          target_api_call_id: mapping.target_api_call_id,
          target_db_connection_id: mapping.target_db_connection_id,
          target_table: mapping.target_table,
          fields: mapping.fields
        });
      });
    }
  }

  onSubmit(): void {
    if (this.form.invalid) return;

    const payload = this.form.value;
    if(this.mappingId) {
      this.mappingService.update(this.mappingId,payload).subscribe(() => {
        this.globalSuccessService.show('El mapeo entre conexiones se ha editado correctamente.', 'Sincronización correcta');
        this.router.navigate(['/connections/mappings']);
      });
    } else {
      this.mappingService.create(payload).subscribe(() => {
        this.globalSuccessService.show('El mapeo entre conexiones se ha creado correctamente.', 'Sincronización correcta');
        this.router.navigate(['/connections/mappings']);
      });
    }
  }
}
