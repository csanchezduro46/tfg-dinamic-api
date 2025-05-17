import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CallMappingCreatePageComponent } from './call-mapping-create-page.component';

describe('CallMappingCreatePageComponent', () => {
  let component: CallMappingCreatePageComponent;
  let fixture: ComponentFixture<CallMappingCreatePageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CallMappingCreatePageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CallMappingCreatePageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
