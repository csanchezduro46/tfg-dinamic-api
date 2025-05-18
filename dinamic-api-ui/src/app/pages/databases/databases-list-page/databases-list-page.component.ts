import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { faPen, faTrash } from '@fortawesome/free-solid-svg-icons';
import { DatabaseConnectionService } from '../../../shared/services/api/database-connection.service';
import { AuthService } from '../../../shared/services/oauth/auth.service';
import { ConfirmPopupComponent } from '../../../shared/ui/confirm-popup/confirm-popup.component';
import { DatabaseFormComponent } from '../database-form/database-form.component';
import { GlobalSuccessService } from '../../../shared/services/generic/global-success.service';

@Component({
  standalone: true,
  selector: 'app-databases-list-page',
  imports: [CommonModule, FontAwesomeModule, ConfirmPopupComponent, DatabaseFormComponent],
  templateUrl: './databases-list-page.component.html',
  styleUrl: './databases-list-page.component.css'
})
export class DatabasesListPageComponent implements OnInit {
  databases: any[] = [];
  loading = false;
  admin: boolean = false;

  editing: any = null;
  confirmDeleteId: number | null = null;
  showFormPopup: boolean = false;
  loadingConnection: boolean = false;

  constructor(private readonly dbService: DatabaseConnectionService, library: FaIconLibrary,
    private readonly auth: AuthService, private readonly globalSuccessService: GlobalSuccessService) {
    library.addIcons(faPen, faTrash);
  }

  ngOnInit(): void {
    this.admin = this.auth.hasRole('admin');
    this.fetchDatabases();
  }

  fetchDatabases(): void {
    this.loading = true;
    if (this.admin) {
      this.dbService.getAll()
        .subscribe({
          next: (data) => {
            this.databases = data;
            this.loading = false;
          },
          error: () => {
            this.loading = false;
            alert('Error al cargar las conexiones de BBDD');
          }
        });
    } else {
      this.dbService.getMine()
        .subscribe({
          next: (data) => {
            this.databases = data;
            this.loading = false;
          },
          error: () => {
            this.loading = false;
            alert('Error al cargar las conexiones de BBDD');
          }
        });
    }
  }

  onEdit(platform: any): void {
    this.editing = platform;
    this.showFormPopup = true;
  }

  onNew(): void {
    this.editing = null;
    this.showFormPopup = true;
  }

  onSaved(): void {
    this.showFormPopup = false;
    const msg = this.editing ? 'La conexión a la base de datos ha sido editada correctamente.' : 'La conexión a la base de datos ha sido creada correctamente.';
    this.globalSuccessService.show(msg, 'Operación realizada correctamente');
    this.editing = null;
    setTimeout(() => {
      this.fetchDatabases();
    }, 1000);
  }

  askDelete(id: number): void {
    this.confirmDeleteId = id;
  }

  confirmDelete(): void {
    if (this.confirmDeleteId) {
      this.dbService.delete(this.confirmDeleteId).subscribe({
        next: () => {
          this.databases = this.databases.filter(db => db.id !== this.confirmDeleteId);
          this.confirmDeleteId = null;
          this.fetchDatabases();
        }
      });
    }
  }

  testConnection(db: any) {
    this.loadingConnection = true;
    this.dbService.test(db.id).subscribe({
      next: () => {
        this.globalSuccessService.show('La conexión a la base de datos es correcta.', 'Conexión correcta');
        this.loadingConnection = false;
      }, error: () => this.loadingConnection = false
    })
  }
}
