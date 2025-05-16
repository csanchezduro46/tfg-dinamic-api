import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CallMappingsPageComponent } from './call-mappings-page.component';

describe('CallMappingsPageComponent', () => {
  let component: CallMappingsPageComponent;
  let fixture: ComponentFixture<CallMappingsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CallMappingsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CallMappingsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
