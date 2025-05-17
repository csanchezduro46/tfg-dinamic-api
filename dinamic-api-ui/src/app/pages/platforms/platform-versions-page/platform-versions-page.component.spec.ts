import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PlatformVersionsPageComponent } from './platform-versions-page.component';

describe('PlatformVersionsPageComponent', () => {
  let component: PlatformVersionsPageComponent;
  let fixture: ComponentFixture<PlatformVersionsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PlatformVersionsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PlatformVersionsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
