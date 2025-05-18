import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExternalIntegrationCreatePageComponent } from './external-integration-create-page.component';

describe('ExternalIntegrationCreatePageComponent', () => {
  let component: ExternalIntegrationCreatePageComponent;
  let fixture: ComponentFixture<ExternalIntegrationCreatePageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExternalIntegrationCreatePageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ExternalIntegrationCreatePageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
