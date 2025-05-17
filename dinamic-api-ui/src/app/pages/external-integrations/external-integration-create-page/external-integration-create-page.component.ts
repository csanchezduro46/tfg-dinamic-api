import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { PlatformNecessaryKey } from '../../../core/models/platform-necessary-key.model';
import { PlatformConnectionCredentialsService } from '../../../shared/services/api/platform-connection-credentials.service';
import { PlatformConnectionService } from '../../../shared/services/api/platform-connection.service';
import { PlatformNecessaryKeysService } from '../../../shared/services/api/platform-necessary-keys.service';
import { PlatformVersionService } from '../../../shared/services/api/platform-version.service';
import { PlatformService } from '../../../shared/services/api/platform.service';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';

@Component({
  selector: 'app-external-integration-create-page',
  templateUrl: './external-integration-create-page.component.html',
  styleUrl: './external-integration-create-page.component.css',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, RouterModule]
})
export class ExternalIntegrationCreatePage implements OnInit {
  step = 1;
  connectionId: number | null = null;

  connectionForm!: FormGroup;
  credentialsForm!: FormGroup;

  platforms: any[] = [];
  versions: any[] = [];
  necessaryKeys: PlatformNecessaryKey[] = [];
  platformName: string = '';

  editingCredentials: Record<string, any> = {};
  loadingConnection: boolean = false;
  loadingCredentials: boolean = true;

  constructor(
    private readonly fb: FormBuilder,
    private readonly connectionService: PlatformConnectionService,
    private readonly connectionCredentialsService: PlatformConnectionCredentialsService,
    private readonly platformsService: PlatformService,
    private readonly versionsService: PlatformVersionService,
    private readonly necessaryKeysService: PlatformNecessaryKeysService,
    private readonly router: Router,
    private readonly globalSuccessService: GlobalSuccessService,
    private readonly route: ActivatedRoute,
    private readonly auth: AuthService
  ) { }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');

    this.platformsService.getAll().subscribe(p => (this.platforms = p));
    this.versionsService.getAll().subscribe(v => (this.versions = v));

    this.connectionForm = this.fb.group({
      user_id: [this.auth.getUser().id || null, Validators.required],
      name: ['', Validators.required],
      platform_id: ['', Validators.required],
      platform_version_id: ['', Validators.required],
      store_url: ['', Validators.required],
    });

    this.credentialsForm = this.fb.group({});

    this.connectionForm.get('platform_id')?.valueChanges.subscribe((value) => {
      this.connectionForm.get('platform_version_id')?.setValue('');
      this.loadingCredentials = true;
      this.loadKeysForPlatform(value);
      this.filteredVersions();
    });

    if (id) {
      // this.step = 2;
      this.connectionId = +id;
      this.connectionService.get(this.connectionId).subscribe((conn) => {
        this.loadingCredentials = true;
        this.connectionForm.patchValue({
          name: conn.name,
          platform_id: conn.version.platform_id,
          platform_version_id: conn.platform_version_id,
          store_url: conn.store_url
        });
        this.platformName = conn.version.platform.name;

        if (conn.credentials) {
          this.editingCredentials = conn.credentials;
        }

        this.loadKeysForPlatform(conn.version.platform_id);
      });
    }
  }

  loadKeysForPlatform(platformId: number): void {
    this.necessaryKeysService.get(platformId).subscribe(response => {
      this.platformName = response.platform;
      this.necessaryKeys = response.necessary_keys;

      const controls: Record<string, any> = {};
      this.necessaryKeys.forEach(field => {
        controls[field.key] = field.required
          ? ['', Validators.required]
          : [''];
      });

      this.credentialsForm = this.fb.group(controls);

      // Preload si estás en edición
      if (Object.keys(this.editingCredentials).length > 0) {
        this.credentialsForm.patchValue(this.editingCredentials);
      }
      this.loadingCredentials = false;
    });
  }

  filteredVersions() {
    const platformId = this.connectionForm.get('platform_id')?.value;
    this.versionsService.getByPlatform(platformId).subscribe({
      next: (versions) => this.versions = versions
    });
  }

  createConnection(): void {
    if (this.connectionForm.invalid) return;

    if (this.connectionId) {
      this.connectionService.update(this.connectionId, this.connectionForm.value).subscribe({
        next: (res) => {
          this.step = 2;
        },
      });
    } else {
      this.connectionService.create(this.connectionForm.value).subscribe({
        next: (res) => {
          this.connectionId = res.id || res.connection?.id;
          this.step = 2;
        },
      });
    }
  }

  saveCredentials(): void {
    if (!this.connectionId || this.credentialsForm.invalid) return;

    this.connectionCredentialsService.create(this.connectionId, this.credentialsForm.value).subscribe({
      next: () => {
        this.globalSuccessService.show('Credenciales guardadas correctamente.');
        this.router.navigate(['/external-integrations']);
      },
    });
  }

  testConnection() {
    if (!this.connectionId) return;
    this.loadingConnection = true;
    this.connectionCredentialsService.test(this.connectionId).subscribe({
      next: () => {
        this.globalSuccessService.show('La conexión externa es correcta.', 'Conexión correcta');
        this.loadingConnection = false;
      }, error: () => this.loadingConnection = false
    });
  }
}
