import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PlatformApisPageComponent } from './platform-apis-page.component';

describe('PlatformApisPageComponent', () => {
  let component: PlatformApisPageComponent;
  let fixture: ComponentFixture<PlatformApisPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PlatformApisPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PlatformApisPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
