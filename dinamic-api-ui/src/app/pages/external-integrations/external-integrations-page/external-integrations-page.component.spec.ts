import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExternalIntegrationsPageComponent } from './external-integrations-page.component';

describe('ExternalIntegrationsPageComponent', () => {
  let component: ExternalIntegrationsPageComponent;
  let fixture: ComponentFixture<ExternalIntegrationsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExternalIntegrationsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ExternalIntegrationsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
