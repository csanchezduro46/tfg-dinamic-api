import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DatabaseSchemaPageComponent } from './database-schema-page.component';

describe('DatabaseSchemaPageComponent', () => {
  let component: DatabaseSchemaPageComponent;
  let fixture: ComponentFixture<DatabaseSchemaPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DatabaseSchemaPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DatabaseSchemaPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
