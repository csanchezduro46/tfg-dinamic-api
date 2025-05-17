import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CallMappingFieldsPageComponent } from './call-mapping-fields-page.component';

describe('CallMappingFieldsPageComponent', () => {
  let component: CallMappingFieldsPageComponent;
  let fixture: ComponentFixture<CallMappingFieldsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CallMappingFieldsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CallMappingFieldsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
